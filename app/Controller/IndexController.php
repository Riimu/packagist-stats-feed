<?php

namespace App\Controller;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class IndexController extends BaseController
{
    public function indexAction()
    {
        $this->view->getChildView('index', 'content');
        $this->view->pageTitle = 'Packagist Stats Feed';
    }

    public function infoAction()
    {
        $this->view->getChildView('info', 'content');
        $this->view->pageTitle = 'User Agent Info';
    }
}
