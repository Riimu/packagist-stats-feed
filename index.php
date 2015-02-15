<?php

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set("log_errors", "1");
ini_set("error_log", __DIR__ . "/var/error.log");

require 'vendor/autoload.php';

define('BASEDIR', __DIR__);

$loader = new \Riimu\Kit\ClassLoader\FileCacheClassLoader(__DIR__ . '/var/class_cache.php');
$loader->addPrefixPath([
    'Riimu\\Braid\\' => __DIR__ . '/lib/Braid/',
    'Riimu\\Kit\\FileResponse\\' => __DIR__ . '/lib/FileResponse/',
    'App\\' => __DIR__ . '/app/',
]);
$loader->register();

$application = new \Riimu\Braid\Application\Application(
    new \Riimu\Braid\Application\Router(require __DIR__ . '/config/routes.php'),
    new \Riimu\Braid\Application\Container(require __DIR__ . '/config/dependencies.php')
);

try {
    $application->run();
} catch (\Riimu\Braid\Application\RouteNotFoundException $ex) {
    \App\ApplicationLogger::logAccess(__DIR__ . '/var/404.log');
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found', true, 404);
    exit('404 Not Found: The page you are looking for does not appear to be here');
} catch (PDOException $ex) {
    \App\ApplicationLogger::logException($ex, __DIR__ . '/var/database.log');
    header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
    exit('500 Internal Server Error: Database error occurred');
} catch (\Riimu\Kit\CSRF\InvalidCSRFTokenException $ex) {
    \App\ApplicationLogger::logAccess(__DIR__ . '/var/csrf.log');
    header($_SERVER["SERVER_PROTOCOL"] . ' 400 Bad Request', true, 400);
    exit('400 Bad Request: Invalid CSRF token - please go back, refresh and try again');
}
