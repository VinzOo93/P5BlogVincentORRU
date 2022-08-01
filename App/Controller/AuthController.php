<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\AuthManager;
use App\Router\Request;
class AuthController
{
    public static function showFormLogIn($message = null)
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('auth/formLogIn.html.twig', ['message' => $message]);
    }

    public static function launchSession(array $data = [])
    {
        $request = new  Request();
        $authManager = new AuthManager();

        if (isset($data)) {
            $email = $data['email'];
            $password = $data['password'];

            $userForChecking = $authManager->checkForLogIn($email);
                if ($userForChecking) {
                    $encryptedPassword = $userForChecking['password'];

                    if (password_verify($password,$encryptedPassword)){
                        $_SESSION["userId"] = $userForChecking['id_user'];
                        $_SESSION["userRole"] = $userForChecking['role'];

                        $request->redirectToRoute('blogIndex');
                    } else {
                        $request->redirectToRoute('login', ['error' => 'Saisie incorrect vérifiez votre mail et votre mot de passe']);
                    }

                } else {
                $request->redirectToRoute('login', ['error' => 'Saisie incorrect vérifiez votre mail et votre mot de passe']);
            }
        }
    }

    public static function logout()
    {
        $request = new  Request();
        $functionHelper = new FunctionHelper();
        $sessionOK = $functionHelper->mustBeAuthentificated();

        if ($sessionOK) {
            if (isset($_SESSION)) {
                session_unset();
                session_destroy();
                session_write_close();
            }
            $request->redirectToRoute('blogIndex');
        }
    }
}