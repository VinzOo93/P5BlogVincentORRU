<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Manager\ArticleManager;
use App\Manager\CommentManager;
use App\Router\Request;

class CommentController
{
    public static function addComment($data, $slug)
    {
        $functionHelper = new FunctionHelper();
        $commentManager = new CommentManager();
        $articleManager = new ArticleManager();
        $request = new Request();

        $sessionOK = $functionHelper->mustBeAuthentificated();

        if ($sessionOK) {
            $user = $functionHelper->checkActiveUserInSession();
            $idUser = $user['id_user'];
            $content = $data['content'];
            $dateAdded = new \DateTime('NOW');
            $dateAdded = $dateAdded->setTimezone(new \DateTimeZone('Europe/Paris'));
            $article = $articleManager->selectOneArticle($slug);
            $idArticle = $article['id_article'];

            $commentManager->insertComment(
                $idUser,
                $content,
                $dateAdded->format('Y-m-d H:i:sP'),
                $idArticle
            );
            $request->redirectToRoute('article', ['success' => "Votre commentaire a été ajouté !" , 'slug' => $slug]);
        }
    }
}