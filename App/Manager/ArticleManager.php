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
    private string $id = 'id_user';
    private string $all = '*';

    public function selectAllArticles()
    {
        $leftJoins = [$this->id => $this->user];

        $columns = [$this->author => $this->id];

        $orderBy = "ORDER BY $this->datePublished DESC";

        return $this->fetchAllWithLeftJoin($this->all, $this->article, $leftJoins, $columns, $orderBy);
    }

    public function selectOneArticle($article)
    {

        $leftJoins = [$this->id => $this->user];

        $columns = [$this->author => $this->id];

        $where = [$this->slug => $article];

        return $this->fetchOneWithLeftJoin($this->all,$this->article,$leftJoins, $columns, $where);

    }

    public function insertArticle($title,$slug,$tags,$image,$content,$datePublished,$author)
    {
         $this->insert($this->article,
            [
                $this->title => $title,
                $this->slug => $slug,
                $this->tags => $tags,
                $this->image => $image,
                $this->content => $content,
                $this->datePublished => $datePublished,
                $this->author => $author
            ]);
    }

}