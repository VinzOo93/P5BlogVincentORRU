<?php

namespace App\Router;

use App\Helper\FunctionHelper;

class Router
{
    private $url;
    private $route;
    private $param;
    private $routeArr;
    private $routes = [];

    /**
     * @param mixed $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function get(array $paramList = [])
    {

        if (!isset($paramList)) {
            throw new \Exception("Get :La clé du tableau n'éxiste pas");
        } else {
            $class = $paramList[1];
            $paramMethod = $paramList[2];
            $methodArr = get_class_methods($class);
            
            if (in_array($paramMethod, $methodArr)) {
                return call_user_func([$class, $paramMethod],$this->param);
            } else {
                throw new \Exception("pas de fonction trouvée");
            }
        }
    }

    public function post(array $paramList = [], array $data = [])
    {
        if (!isset($paramList)) {
            throw new \Exception("Post : La clé du tableau n'éxiste pas");
        } else {
            $class = $paramList[1];
            $paramMethod = $paramList[2];
            $methodArr = get_class_methods($class);
            $data = $_POST;
            $id = $this->param;
            if (in_array($paramMethod, $methodArr)) {

                return call_user_func([$class, $paramMethod], $data, $id);
            } else {
                throw new \Exception("pas de fonction trouvée");
            }
        }
    }

    /**
     * @throws RouterException
     */
    public function run()
    {
        $routeItem = new Route();
        $this->routes = $routeItem->getRoutes();
        if (!isset($this->routes)) {
            throw new  RouterException('No routes matchs');
        } else {
            if ($this->match() === true) {
                $this->call();
            } else {
                throw new  RouterException('No routes matchs');
            }
        }
    }

    public function match(): bool
    {
        $needle = new FunctionHelper();
        $explodeUrl = explode('/', rtrim($this->url));

        foreach ($this->routes as $routeArr) {

            foreach ($routeArr as $route) {
                $explodeRoute = explode('/', ($route));

                if (count($explodeUrl) > 1 && count($explodeRoute) > 1
                    && array_slice($explodeUrl, 0, count($explodeUrl)-1) === array_slice($explodeRoute, 0, count($explodeRoute)-1)) {
                   $needle = $needle->get_string_between(implode('/',$explodeRoute), '{','}');
                   if (array_search('{'.$needle.'}', $explodeRoute)) {
                        $this->param = substr($this->url, stripos($this->url, '/' ) + 1);
                        $route = array_replace($explodeRoute, $explodeUrl);
                        $route = implode("/", $route);
                    }
                }
                if ($route === $this->url) {
                    $this->route = $route;
                    $this->routeArr = $routeArr;
                }
            }
        }

        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->url);
        $regex = "#^" . trim($path, '/') . "#i";

        if (!preg_match($regex, $this->route)) {
            return false;
        } else {
            return true;
        }
    }

    public function call()
    {
        switch ($this->routeArr[3]) {
            case "GET" :
                $this->get($this->routeArr);
                break;
            case "POST" :
                $this->post($this->routeArr);
        }
    }
}