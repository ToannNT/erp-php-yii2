<?php

namespace api\modules\v1\admin\setting\models;

use common\models\DeliveryFee as BaseDeliveryFee;

class DeliveryFee extends BaseDeliveryFee
{
    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['name'], 'unique', 'filter' => [
                '!=', 'status', BaseDeliveryFee::STATUS_DELETE
            ]],
            ["price", "number", "min" => 1],
            ["status", "default", "value" => BaseDeliveryFee::STATUS_INACTIVE],
            ["status", "in", "range" => [BaseDeliveryFee::STATUS_ACTIVE, BaseDeliveryFee::STATUS_INACTIVE]]
        ];
    }
}
