<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Router\Request;
use Exception;

class ArticleController
{
    public static function showArticle($id)
    {
        $twig = new TwigHelper();

        $articleManager = new ArticleManager();

        $article = $articleManager->selectOneArticle($id);

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
        try {
            $title = $data['title'];
            $tags = $data['tag'];
            $image = 'img-article-3';
            $content = $data['content'];
            $datePublished = new \DateTime('NOW');
            $author = 7;

            $articleManager = new ArticleManager();
            $articleManager->insertArticle(
                $title,
                $tags,
                $image,
                $content,
                $datePublished->format('d/m/y H:i:s'),
                $author);

            $request->redirectToRoute('blogIndex');
            echo 'Le nouvel article a été ajouté avec succès <br>';
        } catch (Exception $e) {
            echo 'erreur lors de l\'ajout' . $e;
        }


    }

}