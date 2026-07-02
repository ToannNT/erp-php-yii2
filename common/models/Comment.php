<?php

namespace common\models;

use Yii;
use common\models\base\Comment as BaseComment;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "comment".
 */

class Comment extends BaseComment
{
    const STATUS_DELETE = -99;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const TYPE_PRODUCT = "product";
    const TYPE_ARTICLE = "article";

    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function getTypes(){
        return [
            self::TYPE_ARTICLE,
            self::TYPE_PRODUCT
        ];
    }


    public function getUser()
    {
        return $this->hasOne(User::class, ["id" => "user_id"]);
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
}
