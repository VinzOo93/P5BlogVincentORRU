<?php

namespace App\Router;

use App\Router\Route;

class Request
{

    public function redirectToRoute($index, $param = null)
    {
        $route = new Route();
        $getRoute = $route->getRoutes();

        if ($getRoute != null) {
            $request = $getRoute[$index];
            $class = $request[1];
            $paramMethod = $request[2];
            $methodArr = get_class_methods($class);

            if (in_array($paramMethod, $methodArr)) {
                return call_user_func([$class, $paramMethod], $param);
            }
        }
    }
}