<?php

namespace App\Controller;

use App\Helper\FunctionHelper;
use App\Helper\TwigHelper;
use App\Manager\ArticleManager;
use App\Manager\CommentManager;
use App\Manager\UserManager;
use App\Router\Request;
use App\Validator\ArticleCreationValidator;
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
                $title = $data['title'];
                $tags = trim($data['tag']);
                $content = $data['content'];

                if ($_FILES['image']['size'] != 0) {
                    $slugImageToSlug = $functionHelper->uploadImage($newDirPath);
                    $slugImageToSlug = implode($slugImageToSlug);
                } else {
                    $slugImageToSlug = false;
                }

                $articleCreation = [
                    'title' => $title,
                    'tags' => $tags,
                    'content' => $content,
                    'image' => $slugImageToSlug
                ];

                if ($articleValidator->validate($articleCreation)) {

                    $slug = strtolower(preg_replace('/\s+/', '-', $title));
                    $slugReady = $functionHelper->removeSpecialAndAccent($slug);
                    $datePublished = new \DateTime('NOW');
                    $datePublished = $datePublished->setTimezone(new \DateTimeZone('Europe/Paris'));
                    $author = $_SESSION['userId'];
                    $articleManager = new ArticleManager();
                    $articleManager->insertArticle(
                        $title,
                        $slugReady,
                        $tags,
                        $slugImageToSlug,
                        $content,
                        $datePublished->format('Y-m-d H:i:sP'),
                        $author
                    );
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
        $articleManager = new ArticleManager();
        $imageArray = $articleManager->selectImagePath($slug);
        $idArticle = $articleManager->selectIdArticleBySlug($slug);
        $idArticle = $idArticle['id_article'];
        $imagePath = $imageArray['image'];
        $functionHelper = new FunctionHelper;
        $request = new  Request();
        $pathUploadDir = "../public/images/";
        $sessionOK = $functionHelper->mustBeAuthentificated();

        try {
            if ($sessionOK) {

                $title = $data['title'];
                $tags = trim($data['tag']);
                $content = $data['content'];


                if (!empty($tags) && strpos($tags, ';') === false) {
                    $request->redirectToRoute('showFormUpdateArticle',
                        [
                            'slug' => $slug,
                            'error' => 'Veuillez remplir le champ tag comme suivi => bateau;chat;chocolat',
                        ]);
                } else {
                    if (strlen($title) > 255 || strlen($tags) > 255 || strlen($_FILES['image']['name']) > 255) {
                        $request->redirectToRoute('showFormUpdateArticle',
                            [
                                'slug' => $slug,
                                'error' => "Le champ et le nom de l'image doit être inférieur à 255 caractères",
                            ]);
                    } else {
                        if (!empty($title) && !empty($content)) {
                            if (empty($tags)) {
                                $tags = ' ';
                            }
                            $slug = strtolower(preg_replace('/\s+/', '-', $title));
                            $slugReady = $functionHelper->removeSpecialAndAccent($slug);

                            if ($_FILES['image']['size'] != 0) {
                                $fileImage = "$pathUploadDir$imagePath";
                                if (file_exists($fileImage)) {
                                    $folder = substr($imagePath, 0, strpos($imagePath, '/', 10));
                                    $folderPath = "$pathUploadDir$folder";
                                    unlink($fileImage);
                                    $slugImageToSlug = $functionHelper->uploadImage($folderPath);
                                    if ($slugImageToSlug === false) {
                                        $request->redirectToRoute('showFormUpdateArticle',
                                            [
                                                'slug' => $slug,
                                                'error' => "L'ajout d'image doit être au format JPG",
                                            ]);
                                    }
                                } else {
                                    $request->redirectToRoute('showFormUpdateArticle',
                                        [
                                            'slug' => $slug,
                                            'error' => "L'article enregistré ne possède pas d'image",
                                        ]);
                                }

                            } else {
                                $slugImageToSlug[] = $imagePath;
                            }
                            $registredTitle = $articleManager->selectOneArticleByTitle($title);

                            if (!empty($registredTitle) && $registredTitle['title'] === $title && $idArticle['id_article'] != $registredTitle['id_article']) {
                                $request->redirectToRoute('showFormUpdateArticle',
                                    [
                                        'slug' => $slug,
                                        'error' => "Le titre : $title est déjà utilisé",
                                    ]);
                            } else {
                                $articleManager->updateArticle(
                                    $idArticle,
                                    $title,
                                    $slugReady,
                                    $tags,
                                    implode($slugImageToSlug),
                                    $content
                                );
                                $request->redirectToRoute('blogIndex', ['success' => "L'article '$title' a bien été mis à jour"]);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $request->redirectToRoute('showFormUpdateArticle', ['error' => "Erreur lors de la mis à jour de l'article $e"]);
        }
    }
}
