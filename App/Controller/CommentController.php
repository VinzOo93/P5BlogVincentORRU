<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Manager\ArticleManager;
use App\Manager\CommentManager;
use App\Router\Request;
use App\Validator\CommentCreationValidator;
use Exception;

class CommentController
{
    public static function addComment($data, $slug)
    {
        $functionHelper = new FunctionHelper();
        $commentManager = new CommentManager();
        $articleManager = new ArticleManager();
        $commentValidator = new CommentCreationValidator();
        $request = new Request();


        $sessionOK = $functionHelper->mustBeAuthentificated();

        if ($sessionOK) {
            $user = $functionHelper->checkActiveUserInSession();
            $idUser = $user['id_user'];
            $dateAdded = new \DateTime('NOW');
            $dateAdded = $dateAdded->setTimezone(new \DateTimeZone('Europe/Paris'));
            $article = $articleManager->selectOneArticle($slug);
            $idArticle = $article['id_article'];

            $commentCreation = ['content' => $data['content']];


            if ($commentValidator->validate($commentCreation, $slug)){
                $commentManager->insertComment(
                    $idUser,
                    $commentCreation['content'],
                    $dateAdded->format('Y-m-d H:i:sP'),
                    $idArticle
                );
                $request->redirectToRoute('article', ['success' => "Votre commentaire a été ajouté !" , 'slug' => $slug]);
            }
        }
    }

    public static function deleteComment($data) {
        $commentManager = new CommentManager();
        $request = new Request();
        $functionHelper = new FunctionHelper();
        $id = $data['id'];
        $slug = $data['slug'];

        $sessionOK = $functionHelper->mustBeAuthentificated();
        try {
            if ($sessionOK) {
                $admin = $functionHelper->checkAdminSession();
                if ($admin === false){
                    $commentManager->dropComment($id);
                    $request->redirectToRoute('article',
                        [
                            'success' => "Votre commentaire a été supprimé !" ,
                            'slug' => $slug
                        ]);
                } else {
                    $commentManager->dropComment($id);
                    $request->redirectToRoute('manageArticles',
                        [
                            'success' => "Le commentaire a été supprimé !",
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            $request->redirectToRoute('blogIndex', ['error' => "Erreur lors de la suppression du commentaire $e"]);
        }

    }
}