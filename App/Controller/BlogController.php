<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Helper\FunctionHelper;

class BlogController
{
    public static function showBlog($message = null)
    {
        $twig = new TwigHelper();
        $articleManager = new ArticleManager();
        $functionHelper = new FunctionHelper();

        $articles = $articleManager->selectAllArticles();

        $user = $functionHelper->checkActiveUserInSession();

        $twig->loadTwig()->display('blog/indexBlog.html.twig', ['articles' => $articles, 'user' => $user, 'message' => $message]);
    }
}