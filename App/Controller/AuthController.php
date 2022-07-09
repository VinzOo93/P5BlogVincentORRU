<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\AuthManager;
use App\Router\Request;

class AuthController
{
    private $error = null;

    public static function showFormLogIn($error = null)
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('auth/formLogIn.html.twig', ['error' => $error]);
    }

    public static function launchSession(array $data = [])
    {
        $request = new  Request();
        $authManager = new AuthManager();

        if (isset($data)) {
            $email = $data['email'];
            $password = $data['password'];

            $loggedIn = $authManager->checkForLogIn($email, $password);
                if ($loggedIn) {
                    session_start();
                    $_SESSION["userId"] = $loggedIn['id_user'];
                    $_SESSION["userRole"] = $loggedIn['role'];

                    $request->redirectToRoute('blogIndex');
                } else {
                $request->redirectToRoute('login', ['error' => 'Saisie incorrect vérifiez votre mail et votre mot de passe']);
            }
        }
    }

    public static function logout(){
        $request = new  Request();
        if (isset($_SESSION)){
            session_destroy();
        }
        $request->redirectToRoute('blogIndex');
    }
}