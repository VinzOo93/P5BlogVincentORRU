<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Manager\CommentManager;
use App\Manager\UserManager;
use App\Router\Request;
use App\Validator\ArticleCreationValidator;
use App\Validator\ArticleUpdateValidator;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ArticleController
{
    public static function showArticle($slug)
    {
        $functionHelper = new FunctionHelper();
        $twig = new TwigHelper();
        $articleManager = new ArticleManager();
        $commentManager = new CommentManager();

        if (is_array($slug)) {
            $message = $slug;
            $slug = $slug['slug'];
        } else {
            $message = null;
        }
        $article = $articleManager->selectOneArticle($slug);
        $idArticle = $article['id_article'];
        $comments = $commentManager->selectCommentInArticle($idArticle);
        $user = $functionHelper->checkActiveUserInSession();
        $twig->loadTwig()->display('article/showArticle.html.twig', [
            'message' => $message,
            'article' => $article,
            'user' => $user,
            'comments' => $comments
        ]);
    }

    public static function showFormArticle($message = null)
    {
        $twig = new TwigHelper();
        $functionHelper = new FunctionHelper();

        $sessionOK = $functionHelper->mustBeAuthentificated();
        if ($sessionOK) {
            $title = "Publier un article";

            $user = $functionHelper->checkActiveUserInSession();

            $twig->loadTwig()->display('article/formAddArticle.html.twig', ['message' => $message, 'user' => $user, 'title' => $title]);
        }

    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public static function showFormUpdateArticle($slug)
    {
        $twig = new TwigHelper();
        $functionHelper = new FunctionHelper();
        $articleManager = new ArticleManager();

        $sessionOK = $functionHelper->mustBeAuthentificated();

        if ($sessionOK) {
            $user = $functionHelper->checkActiveUserInSession();

            if (is_array($slug)) {

                $article = $articleManager->selectOneArticle($slug['slug']);
                $message = ['error' => $slug['error']];
            } else {
                $article = $articleManager->selectOneArticle($slug);
                $message = null;
            }
            $articleTitle = $article["title"];
            $title = "Mettre à jour l'article : $articleTitle";

            $twig->loadTwig()->display('article/formUpdateArticle.html.twig', ['message' => $message, 'user' => $user, 'article' => $article, 'title' => $title]);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public static function manageArticles($message = null)
    {
        $twig = new TwigHelper();
        $functionHelper = new FunctionHelper();
        $articleManager = new ArticleManager();
        $commentManager = new CommentManager();
        $userManager = new UserManager();
        $limitArticle = 3;
        $limitComment = 5;
        $limitUsers = 5;
        $sessionOK = $functionHelper->mustBeAuthentificated();
        $role = $_SESSION['userRole'];

        if ($sessionOK) {
            $user = $functionHelper->checkActiveUserInSession();
            if ($role === 'admin') {
                if (
                    isset(
                        $_GET['loadArticle']) &&
                    $_GET['loadArticle'] == '1'
                ) {
                    $offset = $_GET['offset'];
                    $articles = $articleManager->selectAllArticles($limitArticle, $offset);
                    $json = new JsonResponse(
                        [
                            'content' => $twig->loadTwig()->render('article/admin/_list_articles.html.twig',
                                [
                                    'articles' => $articles
                                ])
                        ]
                    );
                    return $json->send();
                } else {
                    $articles = $articleManager->selectAllArticles($limitArticle);
                }

                if (
                    isset(
                        $_GET['loadComments']) &&
                    $_GET['loadComments'] == '1'
                ) {
                    $offset = $_GET['offset'];
                    $comments = $commentManager->selectCommentsForAdmin($limitComment, $offset);
                    $json = new JsonResponse(
                        [
                            'content' => $twig->loadTwig()->render('article/admin/_list_comments.html.twig',
                                [
                                    'comments' => $comments
                                ])
                        ]
                    );
                    return $json->send();
                } else {
                    $comments = $commentManager->selectCommentsForAdmin($limitComment);
                }
                if (
                    isset(
                        $_GET['loadUsers']) &&
                    $_GET['loadUsers'] == '1'
                ) {
                    $offset = $_GET['offset'];
                    $users = $userManager->selectUsersForAdmin($limitUsers, $offset);
                    $json = new JsonResponse(
                        [
                            'content' => $twig->loadTwig()->render('article/admin/_list_users.html.twig',
                                [
                                    'users' => $users
                                ])
                        ]
                    );
                    return $json->send();
                } else {
                    $users = $userManager->selectUsersForAdmin($limitUsers);
                }
            } else {
                if (
                    isset(
                        $_GET['loadArticle']) &&
                    $_GET['loadArticle'] == '1'
                ) {
                    $offset = $_GET['offset'];
                    $articles = $articleManager->selectArticleByUser($user, $limitArticle, $offset);
                    $json = new JsonResponse(
                        [
                            'content' => $twig->loadTwig()->render('article/admin/_list_articles.html.twig',
                                [
                                    'articles' => $articles
                                ])
                        ]
                    );
                    return $json->send();

                } else {
                    $articles = $articleManager->selectArticleByUser($user, $limitArticle);
                }
                $comments = null;
                $users = null;
            }
            $twig->loadTwig()->display('article/manageArticles.html.twig',
                [
                    'user' => $user,
                    'articles' => $articles,
                    'comments' => $comments,
                    'users' => $users,
                    'message' => $message
                ]
            );
        }
    }

    public static function addArticle(array $data = [])
    {
        $functionHelper = new FunctionHelper;
        $articleValidator = new ArticleCreationValidator();
        $request = new  Request();
        $pathUploadDir = '../public/images/articles/';
        $uniq = uniqid();
        $sessionOK = $functionHelper->mustBeAuthentificated();

        try {
            if ($sessionOK) {
                $newDirPath = "$pathUploadDir$uniq";

                $articleCreation = [
                    'title' => $data['title'],
                    'tags' => $data['tags'],
                    'content' => $data['content'],
                ];
                if ($_FILES['image']['size'] != 0) {
                    $slugImageToSlug = $functionHelper->uploadImage($newDirPath);
                    $slugImageToSlug = implode($slugImageToSlug);
                    $articleCreation = array_merge($articleCreation, ['image' => $slugImageToSlug]);
                } else {
                    $articleCreation = array_merge($articleCreation, ['image' => false]);
                }

                if ($articleValidator->validate($articleCreation)) {

                    $slug = strtolower(preg_replace('/\s+/', '-', $articleCreation['title']));
                    $slugReady = $functionHelper->removeSpecialAndAccent($slug);
                    $datePublished = new \DateTime('NOW');
                    $datePublished = $datePublished->setTimezone(new \DateTimeZone('Europe/Paris'));
                    $author = $_SESSION['userId'];
                    $articleManager = new ArticleManager();
                    $articleManager->insertArticle(
                        $articleCreation['title'],
                        $slugReady,
                        $articleCreation['tags'],
                        $articleCreation['image'],
                        $articleCreation['content'],
                        $datePublished->format('Y-m-d H:i:sP'),
                        $author
                    );
                    $title = $data['title'];
                    $request->redirectToRoute('blogIndex', ['success' => "L'article : '$title' a été publié !"]);
                }
            }
        } catch
        (Exception $e) {
            $request->redirectToRoute('newPost', ['error' => "Erreur lors de l\'ajout : $e"]);
        }
    }

    public static function deleteArticle($slug)
    {
        $articleManager = new ArticleManager();
        $request = new Request();
        $functionHelper = new FunctionHelper();
        $pathUploadDir = '../public/images/';
        $sessionOK = $functionHelper->mustBeAuthentificated();

        try {
            if ($sessionOK) {
                $imageArray = $articleManager->selectImagePath($slug);

                if ($imageArray) {
                    $functionHelper->deleteImage($imageArray['image'], $pathUploadDir);
                }

                $articleManager->deleteArticle($slug);
                $request->redirectToRoute('blogIndex', ['success' => "L'article a bien été supprimé"]);
            }
        } catch (Exception $e) {
            $request->redirectToRoute('blogIndex', ['error' => "Erreur lors de la suppression de l'article $e"]);
        }
    }

    public static function updateArticle($data, $slug)
    {
        $articleValidator = new ArticleUpdateValidator();
        $functionHelper = new FunctionHelper;
        $request = new  Request();
        $articleManager = new ArticleManager();
        $imageArray = $articleManager->selectImagePath($slug);
        $idArticle = $articleManager->selectIdArticleBySlug($slug);
        $idArticle = $idArticle['id_article'];
        $imagePath = $imageArray['image'];
        $pathUploadDir = "../public/images/";
        $sessionOK = $functionHelper->mustBeAuthentificated();

        try {
            if ($sessionOK) {
                $articleUpdate = [
                    'title' => $data['title'],
                    'tags' => trim($data['tags']),
                    'content' => $data['content']
                ];
                if ($_FILES['image']['size'] != 0) {
                    $fileImage = "$pathUploadDir$imagePath";
                    if (file_exists($fileImage)) {
                        $folder = substr($imagePath, 0, strpos($imagePath, '/', 10));
                        $folderPath = "$pathUploadDir$folder";
                        unlink($fileImage);
                        $slugImageToSlug = $functionHelper->uploadImage($folderPath);
                        $slugImageToSlug = implode($slugImageToSlug);
                        $articleUpdate = array_merge($articleUpdate, ['image' => $slugImageToSlug]);
                    }
                } else {
                    $articleUpdate = array_merge($articleUpdate, ['image' => $imagePath]);
                }
                if ($articleValidator->validate($articleUpdate, $slug)) {
                    $slug = strtolower(preg_replace('/\s+/', '-', $articleUpdate['title']));
                    $slugReady = $functionHelper->removeSpecialAndAccent($slug);
                    $articleManager->updateArticle(
                        $idArticle,
                        $articleUpdate['title'],
                        $slugReady,
                        $articleUpdate['tags'],
                        $articleUpdate['image'],
                        $articleUpdate['content']
                    );
                    $title = $articleUpdate['title'];
                    $request->redirectToRoute('blogIndex', ['success' => "L'article '$title' a bien été mis à jour"]);
                }
            }
        } catch
        (Exception $e) {
            $request->redirectToRoute('showFormUpdateArticle', ['error' => "Erreur lors de la mis à jour de l'article $e"]);
        }
    }
}
