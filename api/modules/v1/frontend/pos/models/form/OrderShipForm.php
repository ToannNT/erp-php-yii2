<?php

namespace api\modules\v1\frontend\pos\models\form;

use api\modules\v1\admin\person\models\Contact;
use api\modules\v1\frontend\pos\models\OrderShip;
use common\components\shipping\shipper\GHNShipper;
use common\components\shipping\shipper\GHTKShipper;
use common\models\Order;

class  OrderShipForm extends OrderShip
{
    public $order;
    public $service_extras;

    public function rules(): array
    {
        return [
            ["order_id", "filter", "filter" => [$this, "setOrder"]],
            ["sender_province_id", "filter", "filter" => [$this, "setSenderAddress"]],
//            ["receiver_province_id", "filter", "filter" => [$this, "setReceiverAddress"]],
            [["receiver_province_id", "receiver_district_id", "receiver_ward_id", "receiver_phone", "receiver_address", "receiver_name", "receiver_email"], "safe"],
            [["payments", "cod", "weight", "length", "width", "height"], "number", "min" => 0],
            [["transport"], "required"],
            [["transport"], "filter", "filter" => function ($value) {
                return intval($value);
            }],
            ["payment_type", "in", "range" => [self::PAYMENT_TYPE_RECEIVER, self::PAYMENT_TYPE_SENDER]],
            [["shipper_note", "coupon"], "string"],
            ["transport", "in", "range" => [self::TRANSPORT_FLY, self::TRANSPORT_ROAD]],
            [["weight", "length", "width", "height", "value"], "number"],
            [["weight", "length", "width", "height", "value", "insurance_fee"], "filter", "filter" => function ($value) {
                return floatval($value);
            }],
            ["shipper_type", "in", "range" => [GHNShipper::TYPE, GHTKShipper::TYPE]],
            ["weight_option", "required"],
            ["weight_option", "in", "range" => [self::WEIGHT_KG, self::WEIGHT_GRAM]],
            ["service_extras", "filter", "filter" => [$this, "serviceExtrasValidator"]],
            ["value", "filter", "filter" => function () {
                return $this->service_extras[0]["value"] ?? 0;
            }],
            ["shipper_note", "filter", "filter" => function () {
                return $this->service_extras[3]["value"] ?? "";
            }],
            ["payment_type", "filter", "filter" => function () {
                return $this->service_extras[2]["value"] ?? self::PAYMENT_TYPE_SENDER;
            }]
        ];
    }

    public function setOrder()
    {
        $this->order_code = $this->order->id;
        return $this->order->id;
    }

    public function setSenderAddress()
    {
        $office = $this->order->office;
        $contact = Contact::find()->where(["id" => $office->contact_person_id])->one();
        $this->sender_address = $office->address1 ?? null;
        $this->sender_district_id = $office->district_code ?? null;
        $this->sender_ward_id = $office->ward_code ?? null;
        $this->sender_phone = $contact->phone ?? null;
        $this->sender_name = $contact->name ?? null;
        $this->sender_email = $contact->email ?? null;
        return $office->province_code;
    }

    public function serviceExtrasValidator()
    {
        $serviceExtras = $this->serviceExtras();
        if (empty($this->service_extras[0])) {
            return $serviceExtras;
        }
        foreach ($serviceExtras as &$item) {
            foreach ($this->service_extras as $item2) {
                if (empty($item2["code"]) || !isset($item2["value"])) {
                    continue;
                }
                if ($item2["code"] == $item["code"]) {
                    $item["value"] = $item2["value"];
                    unset($item2);
                }
            }
        }
        return $serviceExtras;
    }

    public function selectServiceExtras(): array
    {
        $serviceExtrasSelected = [];
        $serviceExtras = $this->service_extras;
        if (empty($serviceExtras[0])) {
            return [];
        }
        foreach (func_get_args() as $arg) {
            foreach ($serviceExtras as &$item) {
                if ($item["code"] == $arg) {
                    $serviceExtrasSelected[] = $item;
                    unset($item);
                }
            }
        }
        return $serviceExtrasSelected;
    }
}