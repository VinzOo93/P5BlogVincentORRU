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

        if ($functionHelper->mustBeAuthentificated()) {
            $user = $functionHelper->checkActiveUserInSession();
            $idUser = $user['id_user'];
            $dateAdded = new \DateTime('NOW');
            $dateAdded = $dateAdded->setTimezone(new \DateTimeZone('Europe/Paris'));
            $article = $articleManager->selectOneArticle($slug);
            $idArticle = $article['id_article'];

            $commentCreation = ['content' => $data['content']];


            if ($commentValidator->validate($commentCreation, $slug))
            {
                $commentManager->insertComment(
                    $idUser,
                    $comment = $commentCreation['content'],
                    $dateAdded->format('Y-m-d H:i:sP'),
                    $idArticle
                );
                $request->redirectToRoute('article', ['success' => "Votre commentaire '$comment' a bien été soumis pour validation !" , 'slug' => $slug]);
            }
        }
    }

    public static function checkVisibility($data)
    {
        $commentManager = new CommentManager();
        $functionHelper = new FunctionHelper();
        $request = new Request();
        $idComment = $data['comment'];
        $isVisible = $data['visible'];
        try {
            if ($functionHelper->mustBeAuthentificated()) {
                $admin = $functionHelper->checkAdminSession();
                if ($admin === false) {
                    $request->redirectToRoute('blogIndex',
                        [
                        'error' => "Vous n'avez pas de droits administrateur"
                        ]);
                } else {
                    $commentManager->changeVisibility($idComment, $isVisible);
                    $request->redirectToRoute('manageArticles',
                        [
                            'success' => "La visibilité du commentaire a été modifiée avec succès",
                        ]);
                }
            }

        } catch (Exception $e) {
            $request->redirectToRoute('blogIndex',
                [
                'error' => "Erreur lors de la suppression du commentaire $e"
                ]);
        }
    }

    public static function deleteComment($data)
    {
        $commentManager = new CommentManager();
        $request = new Request();
        $functionHelper = new FunctionHelper();
        $id = $data['id'];
        $slug = $data['slug'];
        $author = $data['author'];

        try {
            if ($functionHelper->mustBeAuthentificated()) {
                $admin = $functionHelper->checkAdminSession();
                if ($admin === false) {
                    if ($functionHelper->checkActiveUserInSession() === $author) {
                    $commentManager->dropComment($id);
                    $request->redirectToRoute('article',
                        [
                            'success' => "Votre commentaire a été supprimé !" ,
                            'slug' => $slug
                        ]);
                    } else {
                        $request->redirectToRoute('blogIndex', ['error' => "Vous n'avez pas le doit administrateur"]);
                    }
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