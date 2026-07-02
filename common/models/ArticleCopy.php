<?php

namespace common\models;

use Yii;
use \common\models\base\ArticleCopy as BaseArticleCopy;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "article_copy".
 */
class ArticleCopy extends BaseArticleCopy
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
