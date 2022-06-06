<?php

namespace App\Controller;

use App\Helper\TwigHelper;

class ArticleController
{
    public static function showArticle()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('article/showArticle.html.twig');
    }

    public static function showFormArticle()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('article/formAddArticle.html.twig');
    }
}