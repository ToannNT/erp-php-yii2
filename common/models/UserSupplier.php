<?php

namespace common\models;

use Yii;
use \common\models\base\UserSupplier as BaseUserSupplier;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_supplier".
 */
class UserSupplier extends BaseUserSupplier
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

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
