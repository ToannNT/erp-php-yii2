<?php

namespace api\modules\v1\frontend\order\models;

use common\models\DeliveryMethod as BaseDeliveryMethod;

class DeliveryMethod extends BaseDeliveryMethod
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
            "fee" => function () {
                return (float)$this->fee;
            },
            "is_default" => function () {
                return (int)$this->is_default;
            },
        ];
    }
}
