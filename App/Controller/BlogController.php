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
        $limit = 3;
        $offset = 0;
        $countArticles = $articleManager->countAllArticle();
        $countArticles = $countArticles[0];

        if (!empty($_GET['page'])){
            $offset = $limit * ($_GET['page'] - 1);
        }
        $articles = $articleManager->selectAllArticles($limit, $offset);
        $user = $functionHelper->checkActiveUserInSession();

        $twig->loadTwig()->display('blog/indexBlog.html.twig',
            [
                'articles' => $articles,
                'user' => $user,
                'message' => $message,
                'countArticles' => $countArticles,
                'limit' => $limit
            ]
        );
    }
}