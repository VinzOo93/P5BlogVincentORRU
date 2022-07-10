<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Manager\UserManager;
use App\Router\Request;
use Exception;

class ArticleController
{
    public static function showArticle($article)
    {
        $functionHelper = new FunctionHelper();

        $functionHelper->startSession();

        $twig = new TwigHelper();
        $user = null;
        $articleManager = new ArticleManager();

        $article = $articleManager->selectOneArticle($article);

        if (isset($_SESSION)) {
            $userManager = new UserManager();
            $user = $userManager->selectUser($_SESSION['userId']);
        }

        $twig->loadTwig()->display('article/showArticle.html.twig', ['article' => $article, 'user' => $user]);
    }

    public static function showFormArticle($message = null)
    {

        $twig = new TwigHelper();
        $userManager = new UserManager();


        $user = $userManager->selectUser($_SESSION['userId']);

        $twig->loadTwig()->display('article/formAddArticle.html.twig', ['message' => $message, 'user' => $user]);
    }

    public static function addArticle(array $data = [])
    {
        $functionHelper = new FunctionHelper;
        $request = new  Request();
        $pathUploadDir = '../public/images/articles/';
        $uniq = uniqid();

        try {
            $newDirPath = "$pathUploadDir$uniq";
            $title = $data['title'];
            $tags = trim($data['tag']);
            $content = $data['content'];

            if (!empty($tags) && strpos($tags, ';') === false) {
                $request->redirectToRoute('newPost', ['error' => 'Veuillez remplir le champ tag comme suivi => bateau;chat;chocolat']);
            } else {
                if (!empty($title) && !empty($content)) {

                    $slug = strtolower(preg_replace('/\s+/', '-', $title));
                    $slugReady = $functionHelper->removeSpecialAndAccent($slug);

                    if ($_FILES['image']['size'] != 0) {
                        $imageTmpName = $_FILES['image']['tmp_name'];
                        $imgName = $_FILES['image']['name'];

                            if(pathinfo($imgName, PATHINFO_EXTENSION) === 'jpg') {
                                $imgSlug = "$newDirPath/$imgName";
                                mkdir($newDirPath);
                                move_uploaded_file(
                                    $imageTmpName,
                                    $imgSlug
                                );
                                $slugImageToSlug = str_split($imgSlug, 17);
                                unset($slugImageToSlug[0]);
                            }  else {
                                $request->redirectToRoute('newPost', ['error' => "L'image doit être au format JPG"]);
                           die();
                            }
                    } else {
                        $request->redirectToRoute('newPost', ['error' => 'un article doit comporter une image !']);
                    die();
                    }
                    $datePublished = new \DateTime('NOW');
                    $datePublished = $datePublished->setTimezone(new \DateTimeZone('Europe/Paris'));
                    $author = $_SESSION['userId'];

                    $articleManager = new ArticleManager();
                    $registredTitle = $articleManager->selectOneArticleByTitle($title);

                    if ($registredTitle) {
                        $request->redirectToRoute('newPost', ['error' => "le titre : $title est déjà utilisé"]);
                    } else {
                        $articleManager->insertArticle(
                            $title,
                            $slugReady,
                            $tags,
                            implode($slugImageToSlug),
                            $content,
                            $datePublished->format('Y-m-d H:i:sP'),
                            $author
                        );
                        $request->redirectToRoute('blogIndex', ['success' => "L'article : '$title' a été ajouté avec succès !"]);
                    }
                } else {
                    $request->redirectToRoute('newPost', ['error' => 'un article doit comporter obligatoirement un titre et un contenu']);
                }
            }
        } catch
        (Exception $e) {
            $request->redirectToRoute('newPost', ['error' => "erreur lors de l\'ajout : $e"]);
        }
    }
}