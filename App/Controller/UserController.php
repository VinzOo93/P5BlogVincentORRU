<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\UserManager;
use App\Router\Request;
use Exception;


class UserController
{

    public static function showFormAddUser($message = null)
    {
        $twig = new TwigHelper();
        $twig->loadTwig()->display('user/formAddUser.html.twig', ['message' => $message]);
    }

    public static function addUser(array $data = [])
    {
        $request = new  Request();
        $functionHelper = new FunctionHelper();
        $pathUploadDir = '../public/images/users/';

        $uniq = uniqid();

        try {
            $name = $data['name'];
            $firstName = $data['firstName'];
            $email = $data['email'];
            $role = 'user';
            $password = $data['password'];
            $newDirPath = "$pathUploadDir$uniq";

            if (!empty($name) && !empty($firstName) && !empty($email) && !empty($password)) {
                if (strlen($password) < 6) {
                    $request->redirectToRoute('register', ['error' => "Le mot de passe doit être composé de 6 caractères minimum"]);
                } else {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $userManager = new UserManager();
                        $registredUserMail = $userManager->selectByMail($email);
                        if ($registredUserMail) {
                            $request->redirectToRoute('register', ['error' => "L'identfiant mail est déja utilisé ! "]);
                        } else {
                            $slugImageToSlug = $functionHelper->uploadImage($newDirPath);
                            if ($slugImageToSlug === false) {
                                $request->redirectToRoute('register', ['error' => "L'ajout d'image est obligatoire et doit au être format JPG"]);
                            } else {
                                $userManager->insertUser(
                                    $name,
                                    $firstName,
                                    $email,
                                    $role,
                                    implode($slugImageToSlug),
                                    $password
                                );
                                $request->redirectToRoute('blogIndex', ['success' => "Bravo ! L'utilisateur : $email a bien été ajouté ! Vous pouvez vous connecter"]);
                            }
                        }
                    } else {
                        $request->redirectToRoute('register', ['error' => "Le champ email ne correspond pas à la synthaxe d'une adresse mail"]);
                    }
                }
            } else {
                $request->redirectToRoute('register', ['error' => "Merci de remplir l'intégralité des champs du formulaire !"]);
            }
        } catch (Exception $e) {
            $request->redirectToRoute('register', ['error' => "Erreur Lors de l'enregistrement ! $e"]);
        }
    }

    public static function showUser($id)
    {
        $twig = new TwigHelper();

        $userManager = new UserManager();

        $user = $userManager->selectUser($id);
        $twig->loadTwig()->display('showUser.html.twig', ['user' => $user]);
    }

    public static function updateUser($data, $id)
    {
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

        } catch (Exception $e) {
            echo 'erreur lors de la mise à jour . ' . $e;
        }
    }

    public static function deleteUser($id)
    {
        $userManager = new UserManager();
        $request = new  Request();
        try {
            $userManager->deleteUser($id);
            $request->redirectToRoute('home');
            echo 'utilisateur supprimé <br>';
        } catch (Exception $e) {
            echo 'erreur lors de la suppression' . $e;
        }

    }
}