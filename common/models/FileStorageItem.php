<?php

namespace common\models;

use Yii;
use \common\models\base\FileStorageItem as BaseFileStorageItem;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "file_storage_item".
 */
class FileStorageItem extends BaseFileStorageItem
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false
            ]
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
