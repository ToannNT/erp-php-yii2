<?php

namespace api\modules\v1\frontend\pos\models\form;


use common\components\shipping\shipper\GHNShipper;
use common\components\shipping\shipper\GHTKShipper;

class CheckPriceOrderShip extends OrderShipForm
{

    public function rules(): array
    {
        return [
            ["service_extras", "filter", "filter" => [$this, "serviceExtrasValidator"]],
            ["order_id", "filter", "filter" => [$this, "setOrder"]],
            ["sender_province_id", "filter", "filter" => [$this, "setSenderAddress"]],
            [["receiver_province_id", "receiver_district_id", "receiver_ward_id"], "required"],
            [["cod", "insurance_fee", "value", "weight", "length", "width", "height"], "number", "min" => 0],
            [["transport"], "required"],
            [["transport"], "filter", "filter" => function ($value) {
                return intval($value);
            }],
            [["coupon"], "string"],
            [["cod", "weight", "length", "width", "height", "value"], "number"],
            [["cod", "weight", "length", "width", "height", "value"], "filter", "filter" => function ($value) {
                return floatval($value);
            }],
            ["shipper_type", "in", "range" => [GHNShipper::TYPE, GHTKShipper::TYPE]],
            ["weight_option", "required"],
            [["transport", "shipper_type"], "safe"],
            ["weight_option", "in", "range" => [self::WEIGHT_KG, self::WEIGHT_GRAM]],
            ["value", "filter", "filter" => function () {
                return $this->service_extras[0]["value"] ?? 0;
            }],
            ["payment_type", "filter", "filter" => function () {
                return $this->service_extras[2]["value"] ?? self::PAYMENT_TYPE_SENDER;
            }]
        ];
    }

}