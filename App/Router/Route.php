<?php

namespace App\Router;

use App\Controller\HomeController;
use App\Controller\CrudUserController;

class Route
{

    public function getRoutes()
    {
        return [
            'home' => ['', HomeController::class, 'showHome', 'GET'],
            'register' => ['register', CrudUserController::class, 'showForm', 'GET'],
            'registerUser' => ['registerUser', CrudUserController::class, 'addUser', 'POST'],
            'user' => ['user/{id}', CrudUserController::class, 'showUser', 'GET']
        ];
    }

}

