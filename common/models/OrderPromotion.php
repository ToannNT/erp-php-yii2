<?php

namespace common\models;

use Yii;
use \common\models\base\OrderPromotion as BaseOrderPromotion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_promotion".
 */
class OrderPromotion extends BaseOrderPromotion
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
