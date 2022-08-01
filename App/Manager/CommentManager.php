<?php

namespace App\Manager;

class CommentManager extends QueryManager
{
    private string $comment = 'comment';
    private string $author = 'author';
    private string $content = 'content';
    private string $dateAdded = 'date_added';
    private string $article = 'article';
    private string $user = 'user';
    private string $idUser = 'id_user';
    private string $idArticle = 'id_article';
    private string $all = '*';


    public function insertComment($author, $content, $dateAdded, $article){
        $this->insert(
            $this->comment, [
                $this->author => $author,
                $this->content => $content,
                $this->dateAdded => $dateAdded,
                $this->article => $article
            ]
        );
    }

}