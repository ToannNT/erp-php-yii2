<?php

namespace common\models;

use Yii;
use \common\models\base\Tag as BaseTag;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tag".
 */
class Tag extends BaseTag
{
    public function formName()
    {
        return "";
    }
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'slug' => [
                    'class' => SluggableBehavior::class,
                    'attribute' => 'name',
                    'slugAttribute' => 'slug',
                ],
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
