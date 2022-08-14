<?php

namespace App\Manager;

class CommentManager extends QueryManager
{
    private string $comment = 'comment';
    private string $title = 'title';
    private string $firstName = 'firstname';
    private string $role = 'role';
    private string $name = 'name';
    private string $slug = 'slug';
    private string $author = 'author';
    private string $content = 'content';
    private string $dateAdded = 'date_added';
    private string $article = 'article';
    private string $user = 'user';
    private string $idUser = 'id_user';
    private string $idArticle = 'id_article';
    private string $idComment = 'id_comment';
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

    public function selectCommentInArticle($idArticle){

        $leftJoins = [$this->idUser => $this->user];
        $columns = [$this->author => $this->idUser];
        $where = [$this->article => $idArticle];

     return $this->fetchWithLeftJoin(
                $this->all,
                $this->comment,
                $leftJoins,
                $columns,
                $where
        );
    }

    public function dropComment($idComment)
    {
        $this->delete($this->comment, $this->idComment, $idComment);
    }

    public function selectCommentsForAdmin($limit, $offset = 0){

        $selector = "
            $this->comment.$this->idComment,
            $this->comment.$this->dateAdded,
            $this->comment.$this->content,
            $this->article.$this->slug,
            $this->article.$this->title,
            $this->user.$this->firstName,
            $this->user.$this->name,
            $this->user.$this->role
            ";

        $leftJoins = [
            $this->idUser => $this->user,
            $this->idArticle => $this->article
        ];
        $columns = [
            $this->author => $this->idUser,
            $this->article => $this->idArticle
            ];
        $where = [];

        $order = "ORDER BY $this->comment.$this->dateAdded DESC LIMIT $limit OFFSET $offset";

     return $this->fetchWithLeftJoin(
         $selector,
         $this->comment,
         $leftJoins,
         $columns,
         $where,
         $order
     );

    }

}