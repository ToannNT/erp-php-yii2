<?php

namespace common\models;

use common\behaviors\JsonBehavior;
use Yii;
use \common\models\base\OrderOrderReturn as BaseOrderOrderReturn;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_order_return".
 */
class OrderOrderReturn extends BaseOrderOrderReturn
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
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
