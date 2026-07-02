<?php

namespace common\models;

use Yii;
use \common\models\base\CommentLike as BaseCommentLike;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "comment_like".
 */
class CommentLike extends BaseCommentLike
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
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
