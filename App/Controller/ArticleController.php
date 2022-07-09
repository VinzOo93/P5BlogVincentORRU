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
        session_start();

        $twig = new TwigHelper();
        $user = null;
        $articleManager = new ArticleManager();

        $article = $articleManager->selectOneArticle($article);

        if (isset($_SESSION)){
            $userManager = new UserManager();
            $user = $userManager->selectUser($_SESSION['userId']);
        }

        $twig->loadTwig()->display('article/showArticle.html.twig', ['article' => $article, 'user' => $user]);
    }

    public static function showFormArticle()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('article/formAddArticle.html.twig');
    }

    public static function addArticle(array $data = [])
    {
        $functionHelper = new FunctionHelper;
        $request = new  Request();
        $pathUploadDir = '../public/images/articles/';
        $uniq = uniqid();
        try {
            $newDirPath = "$pathUploadDir$uniq";
            mkdir($newDirPath);
            $title = $data['title'];
            $tags = $data['tag'];
            $slug = strtolower(preg_replace('/\s+/','-', $title ));

            $slugReady = $functionHelper->removeSpecialAndAccent($slug);
            if (isset($_FILES['image'])){
                $imageTmpName = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgSlug = "$newDirPath/$imgName";
                move_uploaded_file(
                    $imageTmpName,
                    $imgSlug
                );
            }
            $slugImageToSlug = str_split($imgSlug, 17);
            unset($slugImageToSlug[0]);
            $content = $data['content'];
            $datePublished = new \DateTime('NOW');
            $datePublished = $datePublished->setTimezone(new \DateTimeZone('Europe/Paris'));
            $author = 1;

            $articleManager = new ArticleManager();
            $articleManager->insertArticle(
                $title,
                $slugReady,
                $tags,
                implode($slugImageToSlug),
                $content,
                $datePublished->format('Y-m-d H:i:sP'),
                $author
            );

            $request->redirectToRoute('blogIndex');
            echo 'Le nouvel article a été ajouté avec succès <br>';
        } catch (Exception $e) {
            echo 'erreur lors de l\'ajout' . $e;
        }


    }

}