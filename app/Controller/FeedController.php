<?php

namespace App\Controller;

use App\ApplicationLogger;
use App\Feed\CurlErrorException;
use App\Feed\Downloader;
use App\Feed\DownloadErrorException;
use App\Feed\PageParser;
use Riimu\Kit\FileResponse\Response\FileResponse;
use Riimu\Kit\FileResponse\ResponseHandler;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FeedController extends BaseController
{
    private static $errorCodes = [
        '404' => 'Not Found',
        '500' => 'Internal Server Error',
    ];

    protected $validateToken = false;

    public function feedAction($user)
    {
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]*$/', $user)) {
            return $this->error(404, 'The page you are looking for does not appear to be here');
        }

        $message = null;

        $feedFile = sprintf('%s/var/feeds/%s.rss', BASEDIR, strtolower($user));
        $touchFile = sprintf('%s/var/touch/%s', BASEDIR, strtolower($user));
        $new = !file_exists($feedFile);

        if ($new || time() - filemtime($touchFile) > 60 * 60) {
            $downloader = new Downloader(BASEDIR . '/var/requests/');
            $pageFile = sprintf('%s/var/pages/%s.html', BASEDIR, strtolower($user));

            try {
                $updated = $downloader->updateFile(sprintf($this->container->get('packagist.profile.url'), $user), $pageFile);

                if ($new || $updated) {
                    header('content-type: text/plain');
                    $parser = new PageParser();
                    list($name, $packages) = $parser->parseFile($pageFile);


                    $storage = $this->container->get('feedStorage');
                    $changed = $storage->store($name, $packages);

                    if ($new || $changed) {
                        $view = $this->view->getChildView('feed');
                        file_put_contents($feedFile, $view->getRender([
                            'name' => $name,
                            'entries' => $storage->getEntries(),
                            'date' => new \DateTime(),
                            'packagistUrl' => $this->container->get('packagist.url'),
                            'escape' => function ($value) {
                                return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
                            },
                        ]), LOCK_EX);
                    }
                }

                touch($touchFile);
            } catch (DownloadErrorException $ex) {
                if ($ex->getCode() == 404) {
                    return $this->error(404, sprintf('The username \'%s\' could not be found in Packagist', $user));
                }

                $message = $ex->getMessage();
            } catch (CurlErrorException $ex) {
                $message = $ex->getMessage();
            }
        }

        if (isset($message)) {
            ApplicationLogger::logError($message, BASEDIR . '/var/feed.log');

            if (!file_exists($feedFile)) {
                return $this->error(500, $message);
            }
        } elseif (!file_exists($feedFile)) {
            ApplicationLogger::logError('Unexpected error occurred', BASEDIR . '/var/feed.log');
            return $this->error(500, 'Unexpected error occurred');
        }

        $response = new FileResponse($feedFile);
        $response->setExpires(filemtime($touchFile) + 60 * 60);

        $responseHandler = new ResponseHandler();
        $responseHandler->send($response, false);
        return false;
    }

    private function error($code, $message)
    {
        header(sprintf('%s %s %s', $_SERVER['SERVER_PROTOCOL'], $code, self::$errorCodes[$code]), true, $code);
        printf('%d %s: %s', $code, self::$errorCodes[$code], $message);
        return false;
    }
}
