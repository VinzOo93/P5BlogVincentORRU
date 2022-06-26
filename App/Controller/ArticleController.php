<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Router\Request;
use Exception;

class ArticleController
{
    public static function showArticle($article)
    {
        $twig = new TwigHelper();
        $articleManager = new ArticleManager();

        $article = $articleManager->selectOneArticle($article);

        $twig->loadTwig()->display('article/showArticle.html.twig', ['article' => $article]);
    }

    public static function showFormArticle()
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('article/formAddArticle.html.twig');
    }

    public static function addArticle(array $data = [])
    {
        $request = new  Request();
        $pathUploadDir = '../public/images/articles/';
        try {
            $title = $data['title'];
            $tags = $data['tag'];
            $slug = preg_replace('/\s+/','-', $title );

            if (isset($_FILES['image'])){
                $imageTmpName = $_FILES["image"]['tmp_name'];
                $imgName = $_FILES["image"]['name'];

            move_uploaded_file($imageTmpName, "$pathUploadDir$imgName" );
            }

            $content = $data['content'];
            $datePublished = new \DateTime('NOW');
            $datePublished = $datePublished->setTimezone(new \DateTimeZone('Europe/Paris'));
            $author = 1;

            $articleManager = new ArticleManager();
            $articleManager->insertArticle(
                $title,
                $slug,
                $tags,
                $imgName,
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