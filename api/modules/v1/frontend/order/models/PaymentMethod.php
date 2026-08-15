<?php

namespace api\modules\v1\frontend\order\models;

use common\models\PaymentMethod as BasePaymentMethod;

class PaymentMethod extends BasePaymentMethod
{
    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "is_default" => function () {
                return (int)$this->is_default;
            },
        ];
    }
}
