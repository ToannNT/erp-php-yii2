<?php

namespace common\models;

use Yii;
use \common\models\base\OrderPaymentMethod as BaseOrderPaymentMethod;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_payment_method".
 */
class OrderPaymentMethod extends BaseOrderPaymentMethod
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

    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ["id" => "payment_method_id"]);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ["id" => "order_id"]);
    }
}
