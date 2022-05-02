<?php

namespace App\Controller;

use App\DataBaseConnexion\PdoConnexion;
use App\Helper\TwigHelper;

class HomeController
{

    static function showHome(){
        $db = PdoConnexion::ConnectToDB();
        $twig = TwigHelper::loadTwig();

        $userStatement = $db->prepare("SELECT name FROM user");
        $userStatement->execute();
        $users = $userStatement->fetchAll();

        return $twig->render('index.html.twig', ['users' => $users]);
    }

}