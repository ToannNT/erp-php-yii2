<?php

namespace api\modules\v1\admin\order\models;

use common\models\OrderPaymentMethod as BaseOrderPaymentMethod;

class OrderPaymentMethod extends BaseOrderPaymentMethod
{
    public function fields()
    {
        return [
            "id",
            "order_id",
            "payment_method_id",
            "payment_method_code" => function () {
                return $this->paymentMethod?->code;
            },
            "payment_method_name" => function () {
                return $this->paymentMethod?->name;
            },
            "payment" => function () {
                return (float)$this->payment;
            },
            "created_at",
            "updated_at",
        ];
    }
}
