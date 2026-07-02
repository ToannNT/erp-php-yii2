<?php

namespace api\modules\v1\frontend\pos\models;

use common\models\OrderShip as BaseOrderShip;
use common\models\Shipper;

class OrderShip extends BaseOrderShip
{
    const UN_SET_INSURE = 100000;

    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return [
            "id",
            "order_id",
            "sender_name",
            "sender_province_id",
            "sender_district_id",
            "sender_ward_id",
            "sender_address",
            "sender_phone",
            "sender_email",
            "receiver_name",
            "receiver_province_id",
            "receiver_district_id",
            "receiver_ward_id",
            "receiver_address",
            "receiver_phone",
            "receiver_email",
            "payments",
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
            "payment_type",
            "expected_delivery_time",
            "partner_code",
            "ship_fee",
            "insurance_fee",
            "total_fee"
        ];
    }

    public function getShipper()
    {
        return parent::getShipper()->addSelect(["id", "name", "thumbnail"]);
    }

    public function createOrder($shipper): bool
    {
        $this->partner_code = $shipper["partner_code"];
        $this->status = $shipper["status"];
        $this->extra_shipper = $shipper["extra_fields"];
        $this->extra_fields = $shipper["service_extras"];
        $this->expected_delivery_time = $shipper["expected_delivery_time"];
        $this->shipper_id = $shipper["id"];
        return $this->save(false);
    }
}