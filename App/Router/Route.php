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
            'logout' => ['logout', AuthController::class, 'logout', 'GET'],
            'launchSession' => ['launchSession', AuthController::class, 'launchSession', 'POST'],
            'registerUser' => ['registerUser', UserController::class, 'addUser', 'POST'],
            'newPost' => ['newPost', ArticleController::class, 'showFormArticle', 'GET'],
            'addArticle' => ['addArticle', ArticleController::class, 'addArticle', 'POST'],
            'deleteArticle' =>['deleteArticle/{slug}', ArticleController::class, 'deleteArticle', 'GET'],
            'manageArticles' => ['manageArticles/{id_user}', ArticleController::class, 'manageArticles', 'GET'],
            'user' => ['user/{id}', UserController::class, 'showUser', 'GET'],
            'article' => ['article/{slug}', ArticleController::class, 'showArticle', 'GET'],
            'updateUser' => ['user/updateUser/{id_user}', UserController::class, 'updateUser', 'POST'],
            'deleteUser' => ['user/deleteUser/{id_user}', UserController::class, 'deleteUser', 'GET']
        ];
    }

}

