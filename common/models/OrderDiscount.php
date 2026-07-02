<?php

namespace common\models;

use common\behaviors\JsonBehavior;
use Yii;
use \common\models\base\OrderDiscount as BaseOrderDiscount;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_discount".
 */
class OrderDiscount extends BaseOrderDiscount
{
    const TYPE_PROMOTION = "promotion";

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    "class" => JsonBehavior::class,
                    'jsonAttributes' => ["condition_items", "extras_fields", "offices"]
                ]
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

    public function formName()
    {
        return "";
    }

}
