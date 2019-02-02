<?php

namespace Core;

/**
 * Router class.
 * The router stocks routes by regex.
 */

class Router {
    private $_routes = [];

    /**
     * Adds a regex with an action to the route list.
     * $url is a regex.
     * $action format is class@random_method
     */
    public function route($url, $action) {
        $this->_routes[$url] = $action;
    }

    /**
     * Executes the route.
     * It will invoke the method on the class passed the action.
     */
    public function execute($url) {
        foreach ($this->_routes as $key => $value) {
            if (preg_match("#^" . $key . "$#", $url) === 1) {
                $exploded = explode("@", $value);
                $reflectionClass = new \ReflectionClass($exploded[0]);
                $controller = $reflectionClass->newInstanceArgs([$url]);
                $method = $reflectionClass->getMethod($exploded[1]);
                $method->invoke($controller);
                return;
            }
        }
        header('HTTP/1.0 404 Not Found');
        echo "<h1 style='text-align:center;'>Error 404 - Page not found</h1>";
    }
}
