<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\UserManager;

class HomeController
{

    public static function showHome()
    {
        $twig = new TwigHelper();
        $userManager = new UserManager();
        $users = $userManager->selectAllUsers();

        $twig->loadTwig()->display('index.html.twig', ['users' => $users]);
    }

}