<?php

namespace App\Manager;

class ArticleManager extends QueryManager
{
    private string $article = 'article';
    private string $title = 'title';
    private string $tags = 'tag';
    private string $image = 'image';
    private string $content = 'content';
    private string $datePublished = 'date_published';
    private string $author = 'author';
    private string $user = 'user';
    private string $id = 'id_user';
    private string $all = '*';

    public function selectAllArticles()
    {
        return $this->fetchAllWithLeftJoin($this->all, $this->article, $this->user, $this->author, $this->id);
    }

    public function selectOneArticle($id)
    {
        return $this->fetchOneWithLeftJoin($this->all,$this->article,$this->user, $this->author, $this->id, $id);

    }

    public function insertArticle($title,$tags,$image,$content,$datePublished,$author)
    {
         $this->insert($this->article,
            [   $this->title => $title,
                $this->tags => $tags,
                $this->image => $image,
                $this->content => $content,
                $this->datePublished => $datePublished,
                $this->author => $author    ]);
    }

}