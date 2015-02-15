<?php

namespace App\Controller;

use Riimu\Braid\Application\Controller;
use Riimu\Kit\CSRF\CSRFHandler;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class BaseController extends Controller
{
    protected $csrf;
    protected $validateToken = true;

    public function setUp()
    {
        parent::setUp();

        $this->view->setViewPath(dirname(__DIR__) . '/View/%s.php');
        $this->view->setView('template');

        // $this->loadTokenValidator();
    }

    private function loadTokenValidator()
    {
        $csrf = new CSRFHandler();
        $csrf->setGenerator($this->container->get('kit.securerandom'));

        if ($this->validateToken) {
            $csrf->validateRequest(true);
        }

        $this->csrf = $csrf;
        $this->view->getToken = function () use ($csrf) {
            static $token;
            return isset($token) ? $token : ($token = $csrf->getToken());
        };
    }
}
