<?php

namespace common\models;

use Yii;
use \common\models\base\KeyStorageItem as BaseKeyStorageItem;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "key_storage_item".
 */
class KeyStorageItem extends BaseKeyStorageItem
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
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
