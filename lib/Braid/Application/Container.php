<?php

namespace Riimu\Braid\Application;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Container
{
    private $dependencies;
    private $loaded;

    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
        $this->loaded = [];
    }

    public function set($dependency, $definition)
    {
        if (isset($this->loaded[$dependency])) {
            unset($this->loaded[$dependency]);
        }

        $this->dependencies[$dependency] = $definition;
    }

    public function get($dependency)
    {
        if (!isset($this->loaded[$dependency])) {
            $this->loaded[$dependency] = $this->getNew($dependency);
        }

        return $this->loaded[$dependency];
    }

    public function getNew($dependency)
    {
        if (!isset($this->dependencies[$dependency])) {
            throw new \InvalidArgumentException("Unknown dependency '$dependency'");
        }

        if (is_string($this->dependencies[$dependency]) || !is_callable($this->dependencies[$dependency])) {
            return $this->dependencies[$dependency];
        }

        return call_user_func($this->dependencies[$dependency], $this);
    }
}
