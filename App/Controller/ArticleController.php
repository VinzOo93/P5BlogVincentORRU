<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Router\Request;
use Exception;

class ArticleController
{
    public static function showArticle($article)
    {
        $functionHelper = new FunctionHelper();
        $twig = new TwigHelper();
        $user = null;
        $articleManager = new ArticleManager();

        $article = $articleManager->selectOneArticle($article);

        $user = $functionHelper->checkActiveUserInSession();

        $twig->loadTwig()->display('article/showArticle.html.twig', ['article' => $article, 'user' => $user]);
    }

    public static function showFormArticle($message = null)
    {
        $twig = new TwigHelper();
        $functionHelper = new FunctionHelper();

        $user = $functionHelper->checkActiveUserInSession();

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

                    $slugImageToSlug = $functionHelper->uploadImage($newDirPath);

                    if ($slugImageToSlug === false) {
                        $request->redirectToRoute('newPost', ['error' => "L'ajout d'image est obligatoire et doit au être format JPG"]);
                    } else {
                        $datePublished = new \DateTime('NOW');
                        $datePublished = $datePublished->setTimezone(new \DateTimeZone('Europe/Paris'));
                        $author = $_SESSION['userId'];

                        $articleManager = new ArticleManager();
                        $registredTitle = $articleManager->selectOneArticleByTitle($title);

                        if ($registredTitle) {
                            $request->redirectToRoute('newPost', ['error' => "Le titre : $title est déjà utilisé"]);
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
                            $request->redirectToRoute('blogIndex', ['success' => "L'article : '$title' a été publié !"]);
                        }
                    }
                } else {
                    $request->redirectToRoute('newPost', ['error' => 'Un article doit comporter obligatoirement un titre et un contenu']);
                }
            }
        } catch
        (Exception $e) {
            $request->redirectToRoute('newPost', ['error' => "Erreur lors de l\'ajout : $e"]);
        }
    }
}