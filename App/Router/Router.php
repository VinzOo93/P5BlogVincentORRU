<?php

namespace App\Router;

class Router
{
    private $url;
    private $routes = [];

    /**
     * @param mixed $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function get($path, $callable) {
        $route = new  Route($path, $callable);
        $this->routes['GET'][] = $route;
    }

    public function post($path, $callable) {
        $route = new  Route($path, $callable);
        $this->routes['POST'][] = $route;
    }

    public  function  run(){
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new  RouterException('No routes matchs');
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if ($route->match($this->url)){
                return $route->call();
            }
        }
        throw new  RouterException('No matching routes');
    }
}