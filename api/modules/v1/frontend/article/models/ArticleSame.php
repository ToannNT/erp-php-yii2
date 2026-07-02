<?php

namespace api\modules\v1\frontend\article\models;

class ArticleSame extends Article
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
        ];
    }
}