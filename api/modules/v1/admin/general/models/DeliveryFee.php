<?php

namespace api\modules\v1\admin\general\models;

use common\models\DeliveryFee as BaseDeliveryFee;

class  DeliveryFee extends BaseDeliveryFee
{

    public function fields()
    {
        return [
            "id",
            "name",
            "price",
        ];
    }

    public function formName(): string
    {
        return "";
    }

}