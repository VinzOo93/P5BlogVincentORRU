<?php

namespace App\Controller;

use App\Helper\TwigHelper;

class AuthController
{
    public static function showFormLogIn()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('auth/formLogIn.html.twig');
    }
}