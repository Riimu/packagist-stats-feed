<?php

namespace Riimu\Braid\Application;

use Riimu\Braid\View\View;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class Controller
{
    /** @var \Riimu\Braid\Application\Router Path router */
    protected $router;

    /** @var \Riimu\Braid\Application\Container Dependency container */
    protected $container;

    /** @var \Riimu\Braid\View\View View for the controller */
    protected $view;

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        $this->view = new View();
        $this->view->link = $this->router;
    }

    public function tearDown($return)
    {
        if ($return !== false && !headers_sent()) {
            $this->view->render();
        }
    }
}
