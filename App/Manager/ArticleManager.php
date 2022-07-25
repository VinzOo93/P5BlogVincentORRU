<?php

namespace App\Manager;

class ArticleManager extends QueryManager
{
    private string $article = 'article';
    private string $title = 'title';
    private string $slug = 'slug';
    private string $tags = 'tag';
    private string $image = 'image';
    private string $content = 'content';
    private string $datePublished = 'date_published';
    private string $author = 'author';
    private string $user = 'user';
    private string $idUser = 'id_user';
    private string $idArticle = 'id_article';
    private string $all = '*';


    public function selectAllArticles()
    {
        $leftJoins = [$this->idUser => $this->user];
        $columns = [$this->author => $this->idUser];
        $where = [];
        $orderBy = "ORDER BY $this->datePublished DESC";

        return $this->fetchWithLeftJoin($this->all, $this->article, $leftJoins, $columns, $where ,$orderBy);
    }

    public function selectIdArticleBySlug($slug){

        return $this->fetchOneNoLeftJoin($this->article, $this->idArticle, [$this->slug => $slug]);

    }


    public function selectOneArticleByTitle($title)
    {

        return $this->fetchOneNoLeftJoin($this->article,"$this->idArticle,$this->title", [$this->title => $title]);

    }

    public function selectArticleByUser($user){
        $leftJoins = [];
        $columns = [];
        $where = [$this->author => $user['id_user']];
        $orderBy = "ORDER BY $this->datePublished DESC";


        return $this->fetchWithLeftJoin($this->all, $this->article, $leftJoins ,$columns , $where, $orderBy);
    }

    public function selectOneArticle($slug)
    {

        $leftJoins = [$this->idUser => $this->user];
        $columns = [$this->author => $this->idUser];
        $where = [$this->slug => $slug];

        return $this->fetchOneWithLeftJoin($this->all,$this->article,$leftJoins, $columns, $where);

    }

    public function selectImagePath($slug)
    {
      return$this->fetchOneNoLeftJoin($this->article, $this->image, [$this->slug => $slug]);
    }

    public function insertArticle($title,$slug,$tags,$image,$content,$datePublished,$author)
    {
         $this->insert(
             $this->article,
            [
                $this->title => $title,
                $this->slug => $slug,
                $this->tags => $tags,
                $this->image => $image,
                $this->content => $content,
                $this->datePublished => $datePublished,
                $this->author => $author
            ]
         );
    }

    public function updateArticle($idArticle,$title,$slug, $tags, $image, $content){

        $this->update(
            $this->article,
            [
                $this->idArticle => $idArticle,
                $this->title => $title,
                $this->slug => $slug,
                $this->tags => $tags,
                $this->image => $image,
                $this->content => $content,
            ]
        );

    }

    public  function deleteArticle($slug)
    {
        $this->delete($this->article,$this->slug, $slug);
    }

}