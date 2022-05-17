<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\UserManager;
use App\Router\Request;
use Exception;


class UserController
{

    public static function showForm()
    {
        $twig = new TwigHelper();
        $twig->loadTwig()->display('formAddUser.html.twig');
    }

    public static function addUser(array $data = [])
    {
        $request = new  Request();
        try {
            $name = $data['name'];
            $firstName = $data['firstName'];
            $email = $data['email'];
            $password = $data['password'];

            $userManager = new UserManager();
            $userManager->insertUser($name, $firstName, $email, $password);

            $request->redirectToRoute('home');
            echo 'Le nouvel utilisateur a été ajouté <br>';
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

    public static function updateUser($data, $id) {
        $request = new  Request();

        try {
            $name = $data['name'];
            $firstName = $data['firstName'];
            $email = $data['email'];
            $password = $data['password'];

            $userManager = new UserManager();
            $userManager->amendUser($id, $name, $firstName, $email, $password);

            $request->redirectToRoute('user', $id);
            echo 'utilisateur modifié <br>';

        } catch (Exception $e){
            echo 'erreur lors de la mise à jour . '.$e;
        }
    }

    public static function deleteUser($id) {
        $userManager = new UserManager();
        $request = new  Request();
        try {
            $userManager->deleteUser($id);
            $request->redirectToRoute('home');
            echo 'utilisateur supprimé <br>';
        } catch (Exception $e) {
            echo 'erreur lors de la suppression' .$e;
        }

    }
}