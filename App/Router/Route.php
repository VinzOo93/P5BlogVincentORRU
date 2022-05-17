<?php

namespace App\Router;

use App\Controller\HomeController;
use App\Controller\UserController;

class Route
{

    public function getRoutes()
    {
        return [
            'home' => ['', HomeController::class, 'showHome', 'GET'],
            'register' => ['register', UserController::class, 'showForm', 'GET'],
            'registerUser' => ['registerUser', UserController::class, 'addUser', 'POST'],
            'user' => ['user/{id}', UserController::class, 'showUser', 'GET'],
            'updateUser' => ['user/updateUser/{id}', UserController::class, 'updateUser', 'POST'],
            'deleteUser' => ['user/deleteUser/{id}', UserController::class, 'deleteUser', 'GET']
        ];
    }

}

