<?php

namespace App\Controller;

use App\Helper\TwigHelper;

class BlogController
{
    public static function showBlog()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('blog/indexBlog.html.twig');
    }
}