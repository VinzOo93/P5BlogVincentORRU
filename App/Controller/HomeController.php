<?php

namespace App\Controller;

use App\Helper\TwigHelper;

class HomeController
{

    public static function showHome()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('index.html.twig');
    }

}