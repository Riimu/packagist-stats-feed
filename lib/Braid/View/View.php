<?php

namespace Riimu\Braid\View;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class View
{
    private $viewPath;
    private $view;
    private $variables;
    private $parent;

    public function __construct()
    {
        $this->viewPath = '%s';
        $this->view = null;
        $this->variables = [];
        $this->parent = null;
    }

    public function setViewPath($path)
    {
        $this->viewPath = $path;
    }

    public function setView($template)
    {
        $this->view = $template;
    }

    public function getChildView($template, $name = null)
    {
        $child = new View();
        $child->viewPath = $this->viewPath;
        $child->view = $template;
        $child->parent = $this;

        if ($name !== null) {
            $this->variables[$name] = $child;
        }

        return $child;
    }

    public function getVariables()
    {
        return $this->parent === null
            ? $this->variables
            : $this->variables + $this->parent->getVariables();
    }

    public function render(array $variables = [], $view = null)
    {
        extract($this->getVariables());

        if (func_num_args() > 0) {
            extract(func_get_arg(0));
        }

        require sprintf($this->viewPath, $view === null ? $this->view : $view);
    }

    public function getRender(array $variables = [], $view = null)
    {
        ob_start();
        $this->render($variables, $view);
        return ob_get_clean();
    }

    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($this->variables[$name])) {
            if ($this->parent === null) {
                throw new \OutOfBoundsException("'$name' variable does not exist in the template");
            }

            return $this->parent->__get($name);
        }

        return $this->variables[$name];
    }

    public function __isset($name)
    {
        return isset($this->variables[$name]);
    }

    public function __unset($name)
    {
        unset($this->variables[$name]);
    }
}
