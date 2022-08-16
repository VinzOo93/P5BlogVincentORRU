<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Manager\UserManager;
use App\Router\Request;
use App\Validator\UserCreationValidator;
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
        $userValidator = new UserCreationValidator();
        $pathUploadDir = '../public/images/users/';
        $uniq = uniqid();

        try {
            $name = $data['name'];
            $firstName = $data['firstName'];
            $email = $data['email'];
            $role = 'user';
            $password = $data['password'];
            $newDirPath = "$pathUploadDir$uniq";

            if ($_FILES['image']['size'] != 0) {
                $slugImageToSlug = $functionHelper->uploadImage($newDirPath);
                $slugImageToSlug = implode($slugImageToSlug);
            } else {
                $slugImageToSlug = null;
            }
            $userCreation = [
                'name' => $name,
                'firstname' => $firstName,
                'email' => $email,
                'password' => $password,
                'picture' => $slugImageToSlug
            ];

            if ($userValidator->validate($userCreation)) {
                $userManager = new UserManager();
                $userManager->insertUser(
                    $name,
                    $firstName,
                    $email,
                    $role,
                    $slugImageToSlug,
                    password_hash($password, PASSWORD_DEFAULT)
                );
                $request->redirectToRoute('blogIndex', ['success' => "Bravo ! L'utilisateur : $email a bien été ajouté ! Vous pouvez vous connecter"]);
            }
        } catch
        (Exception $e) {
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

    public static function deleteUser($data)
    {
        $userManager = new UserManager();
        $request = new  Request();
        $functionHelper = new FunctionHelper();
        $articleManager = new ArticleManager();
        $id = $data['id_user'];
        $user = $userManager->selectUser($id);
        $email = $user['email'];
        $pathUploadDir = '../public/images/';

        $sessionOK = $functionHelper->mustBeAuthentificated();
        try {
            if ($sessionOK) {
                $admin = $functionHelper->checkAdminSession();
                if ($admin === true) {

                    $imageArrayUser = $userManager->selectPicturePath($id);

                    if ($imageArrayUser) {
                        $functionHelper->deleteImage($imageArrayUser['picture'], $pathUploadDir);
                    }

                    $userArticles = $articleManager->selectArticleByUser($user, 0);
                    if ($userArticles) {
                        foreach ($userArticles as $article) {
                            if ($article) {
                                $functionHelper->deleteImage($article['image'], $pathUploadDir);
                            }
                        }
                    }

                    $userManager->deleteUser($id);
                    $request->redirectToRoute('manageArticles',
                        [
                            'success' => "l'utilisateur : '$email' a été supprimé !",
                        ]);
                }
            }
        } catch (Exception $e) {
            $request->redirectToRoute('blogIndex', ['error' => "Erreur lors de la suppression du commentaire $e"]);
        }
    }
}