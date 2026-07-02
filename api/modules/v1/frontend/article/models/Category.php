<?php

namespace api\modules\v1\frontend\article\models;

class Category extends \common\models\ArticleCategory
{
    public function fields()
    {
        return [
            "id",
            "title",
            "slug",
            "status",
            "created_at" => function () {
                return date("Y-m-d", $this->created_at);
            },
            "updated_at" => function () {
                return date("Y-m-d", $this->updated_at);
            }
        ];
    }
}