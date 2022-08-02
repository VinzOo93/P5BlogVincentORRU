<?php

namespace App\Router;

use App\Controller\ArticleController;
use App\Controller\AuthController;
use App\Controller\BlogController;
use App\Controller\CommentController;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Manager\CommentManager;

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
            'showFormUpdateArticle' => ['showFormUpdateArticle/{slug}', ArticleController::class, 'showFormUpdateArticle', 'GET'],
            'addArticle' => ['addArticle', ArticleController::class, 'addArticle', 'POST'],
            'updateArticle' => ['updateArticle/{slug}', ArticleController::class, 'updateArticle', 'POST'],
            'deleteArticle' =>['deleteArticle/{slug}', ArticleController::class, 'deleteArticle', 'GET'],
            'manageArticles' => ['manageArticles', ArticleController::class, 'manageArticles', 'GET'],
            'user' => ['user/{id}', UserController::class, 'showUser', 'GET'],
            'article' => ['article/{slug}', ArticleController::class, 'showArticle', 'GET'],
            'updateUser' => ['user/updateUser/{id_user}', UserController::class, 'updateUser', 'POST'],
            'deleteUser' => ['user/deleteUser/{id_user}', UserController::class, 'deleteUser', 'GET'],
            'addComment' => ['addComment/{slug}', CommentController::class, 'addComment', 'POST'],
            'deleteComment' => ['deleteComment', CommentController::class, 'deleteComment', 'POST']
        ];
    }

}

