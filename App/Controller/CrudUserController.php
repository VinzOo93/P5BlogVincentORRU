<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\UserManager;
use Exception;


class CrudUserController
{

    public static function showForm()
    {
        $twig = new TwigHelper();
        $twig->loadTwig()->display('formAddUser.html.twig');
    }

    public static function addUser(array $data = [])
    {
        try {
            $name = $data['name'];
            $firstName = $data['firstName'];
            $email = $data['email'];
            $password = $data['password'];

            $userManager = new UserManager();
            $userManager->insertUser($name, $firstName, $email, $password);

            echo 'Le nouvel utilisateur a été ajouté <br>';
            echo 'voici les données <pre>' . print_r($_POST) . '</pre> <br>';
            echo '<a href="/">Accueil</a>';
        } catch (Exception $e) {
            echo 'erreur lors de l\'ajout' . $e;
        }
    }

    public static function showUser($id)
    {
        $twig = new TwigHelper();

        $userManager = new UserManager();

        $user = $userManager->selectUser($id);
        $twig->loadTwig()->display('showUser.html.twig', ['user' => $user]);
    }

}