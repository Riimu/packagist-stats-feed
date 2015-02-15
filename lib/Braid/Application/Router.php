<?php

namespace Riimu\Braid\Application;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Router
{
    private $routes;
    private $basePath;
    private $url;
    private $self;

    public function __construct(array $routes)
    {
        $this->basePath = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
        $this->routes = $routes;
        $this->self = null;

        $scheme = empty($_SERVER['REQUEST_SCHEME']) ? 'http' : $_SERVER['REQUEST_SCHEME'];
        $this->url = empty($_SERVER['HTTP_HOST']) ? null : $scheme . '://' . $_SERVER['HTTP_HOST'];
    }

    public function getRequestRoute()
    {
        $requestPath = isset($_GET['path']) ? $_GET['path'] : '';
        $route = $this->getRoute($requestPath);

        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $_GET);

        $canonical = substr($requestPath, 0, 1) === '/' ? $requestPath : '/' . $requestPath;
        if ($canonical !== $route['fullPath']) {
            $this->redirect($route['fullPath'], [], $_GET);
        }

        $this->self = $route['fullPath'];

        return $route;
    }

    public function getRoute($routePath)
    {
        $parts = $this->getPathParts($routePath);

        for ($i = 0, $count = count($parts); $i <= $count; $i++) {
            $path = $this->getCombinedPath(array_slice($parts, 0, $count - $i));

            if (isset($this->routes[$path])) {
                $params = $i > 0 ? array_slice($parts, -$i) : [];

                if ($route = $this->getMatchingRoute($params, $this->routes[$path])) {
                    return $route + [
                        'path'     => $path,
                        'fullPath' => $path . ($params ? implode('/', $params) . '/' : ''),
                        'values'   => $params,
                    ];
                }
            }
        }

        throw new RouteNotFoundException('No matching route found for: /' . implode('/', $parts));
    }

    private function getPathParts($path)
    {
        return array_filter(explode('/', $path), function ($value) {
            return $value != '';
        });
    }

    private function getCombinedPath(array $parts)
    {
        return count($parts) === 0 ? '/' : sprintf('/%s/', implode('/', $parts));
    }

    private function getMatchingRoute(array $params, array $routes)
    {
        if (isset($routes['controller'])) {
            $routes = [$routes];
        }

        foreach ($routes as $route) {
            if (!isset($route['params'])) {
                if (count($params) === 0) {
                    return $route;
                }
            } elseif (is_int($route['params']) && count($params) === $route['params']) {
                return $route;
            } elseif (is_array($route['params']) && count($params) === count($route['params'])) {
                foreach ($route['params'] as $key => $pattern) {
                    if (!preg_match($pattern, $params[$key])) {
                        continue 2;
                    }
                }

                return $route;
            }
        }

        return false;
    }

    public function redirect($path, array $params = [], array $get = [])
    {
        header('Location: ' . $this->url($path, $params, $get), true, 302);
        exit;
    }

    public function url($path, array $params = [], array $get = [])
    {
        return $this->url . $this->path($path, $params, $get);
    }

    public function path($path, array $params = [], array $get = [])
    {
        if ($params != []) {
            $path .= '/' . implode('/', $params);
        }

        $route = $this->getCombinedPath(array_map('urlencode', $this->getPathParts($path)));

        if ($get != []) {
            $route .= '?' . http_build_query($get, '', '&amp;');
        }

        return $this->basePath . $route;
    }

    public function self($url = false)
    {
        return $url ? $this->url($this->self) : $this->path($this->self);
    }
}
