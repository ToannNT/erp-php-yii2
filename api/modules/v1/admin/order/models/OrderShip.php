<?php

namespace api\modules\v1\admin\order\models;

class OrderShip extends \common\models\OrderShip
{
    public function fields()
    {
        return [
            "id",
            "sender_name",
            "sender_province_id",
            "sender_district_id",
            "sender_ward_id",
            "sender_address",
            "sender_phone",
            "payments",
            "sender_email",
            "receiver_name",
            "receiver_province_id",
            "receiver_district_id",
            "receiver_ward_id",
            "receiver_address",
            "receiver_phone",
            "receiver_email",
            "extra_fields" => function () {
                return json_decode($this->extra_fields);
            },
            "extra_shipper" => function () {
                return json_decode($this->extra_shipper);
            },
            "status",
            "cod",
            "shipper_note",
            "shipper" => "shipper",
            "weight",
            "weight_option",
            "length",
            "shipper_type",
            "expected_delivery_time",
            "partner_code",
            "ship_fee",
            "insurance_fee",
            "total_fee",
            "payment_type"
        ];
    }

    public function getShipper()
    {
        return parent::getShipper()->addSelect(["id", "name", "thumbnail"]);
    }
}