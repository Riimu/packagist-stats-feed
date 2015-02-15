<?php

namespace Riimu\Braid\Application;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Application
{
    private $router;
    private $container;

    public function __construct(Router $router, Container $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    public function run()
    {
        $path = $this->router->getRequestRoute(true);

        $controller = new $path['controller'];
        $controller->setRouter($this->router);
        $controller->setContainer($this->container);
        $controller->setUp();
        $return = call_user_func_array([$controller, $path['method']], $path['values']);
        $controller->tearDown($return);
    }
}
