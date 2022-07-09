<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Manager\UserManager;

class BlogController
{
    public static function showBlog()
    {
        $twig = new TwigHelper();
        $user = null;
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAllArticles();

        if (isset($_SESSION)){
            $userManager = new UserManager();
            $user = $userManager->selectUser($_SESSION['userId']);
        }
        $twig->loadTwig()->display('blog/indexBlog.html.twig', ['articles' => $articles, 'user' => $user]);
    }
}