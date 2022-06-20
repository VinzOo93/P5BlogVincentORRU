<?php

namespace App\Router;

use App\Controller\ArticleController;
use App\Controller\AuthController;
use App\Controller\BlogController;
use App\Controller\HomeController;
use App\Controller\UserController;

class Route
{

    public function getRoutes()
    {
        return [
            'home' => ['', HomeController::class, 'showHome', 'GET'],
            'blogIndex' => ['blog', BlogController::class, 'showBlog', 'GET'],
            'register' => ['register', UserController::class, 'showFormAddUser', 'GET'],
            'login' => ['login', AuthController::class, 'showFormLogIn', 'GET'],
            'registerUser' => ['registerUser', UserController::class, 'addUser', 'POST'],
            'newPost' => ['newPost', ArticleController::class, 'showFormArticle', 'GET'],
            'addArticle' => ['addArticle', ArticleController::class, 'addArticle', 'POST'],
            'user' => ['user/{id}', UserController::class, 'showUser', 'GET'],
            'article' => ['article/{title}', ArticleController::class, 'showArticle', 'GET'],
            'updateUser' => ['user/updateUser/{id_user}', UserController::class, 'updateUser', 'POST'],
            'deleteUser' => ['user/deleteUser/{id_user}', UserController::class, 'deleteUser', 'GET']
        ];
    }

}

