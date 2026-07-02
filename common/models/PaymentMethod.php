<?php

namespace common\models;

use Yii;
use \common\models\base\PaymentMethod as BasePaymentMethod;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "payment_method".
 */
class PaymentMethod extends BasePaymentMethod
{

    const CASH_PAYMENT = 1;
    const CARD_PAYMENT = 2;
    const TRANSFER_PAYMENT = 3;

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
