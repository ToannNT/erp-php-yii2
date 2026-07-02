<?php

namespace api\modules\v1\frontend\comment\models;

class Comment extends \common\models\Comment
{

    public function fields(): array
    {
        return array_merge([
            "user_id",
            "rating",
            "title",
            "content",
            "images",
            "type",
            "username" => "userFullname"
        ]);
    }

    public function getUserFullname()
    {
        return $this->user->username;
    }

}