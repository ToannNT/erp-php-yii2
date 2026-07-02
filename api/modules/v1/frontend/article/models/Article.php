<?php

namespace api\modules\v1\frontend\article\models;

class Article extends \common\models\Article
{
    public function fields()
    {
        return [
            "id",
            "slug",
            "title",
            "body",
            "category_id",
            "description" => function () {
                return $this->view;
            },
            "thumbnail_base_url",
            "thumbnail_path",
            "created_by" => "authorName",
            "category" => "category",
            "created_at" => function () {
                return date("Y-m-d", $this->created_at);
            },
            "updated_at" => function () {
                return date("Y-m-d", $this->updated_at);
            },
            "published_at" => function () {
                return date("Y-m-d", $this->published_at);
            },
            "category_slug" => "categorySlug",
            "article_sames" => "articleSames"
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ["id" => "category_id"]);
    }

    public function getArticleSames()
    {
        return $this->hasMany(ArticleSame::class, ["category_id" => "category_id"])
            ->where(["<>", "id", $this->id]);
    }
}