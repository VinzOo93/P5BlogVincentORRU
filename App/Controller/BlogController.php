<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\ArticleManager;

class BlogController
{
    public static function showBlog()
    {
        $twig = new TwigHelper();

        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAllArticles();

        $twig->loadTwig()->display('blog/indexBlog.html.twig', ['articles' => $articles]);
    }
}