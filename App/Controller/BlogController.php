<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Manager\UserManager;

class BlogController
{
    public static function showBlog($message = null)
    {
        $twig = new TwigHelper();
        $articleManager = new ArticleManager();
        $user = null;
        $articles = $articleManager->selectAllArticles();
        if (!empty($_SESSION)){
            $userManager = new UserManager();
            $user = $userManager->selectUser($_SESSION['userId']);
        }
        $twig->loadTwig()->display('blog/indexBlog.html.twig', ['articles' => $articles, 'user' => $user, 'message' => $message]);
    }
}