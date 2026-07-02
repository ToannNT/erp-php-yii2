<?php

namespace common\components\shipping\models;

use common\components\shipping\IShipper;
use common\components\shipping\shipper\GHNShipper;
use common\components\shipping\shipper\GHTKShipper;
use common\models\District;
use common\models\Order;
use common\models\OrderShip;
use common\models\Province;
use common\models\Ward;

/**
 * @property OrderShip $orderShip
 */
class GHTKRequestObject extends ShipperRequestBaseObject
{
    /**
     * @var OrderShip $order_ship
     */
    public $order_ship;
    public $service_extras;
    public $shipper_note;
    public $value;
    public $tags;
    const TRANSPORT = [
        1 => "fly",
        2 => "road"
    ];
    protected $paymentTypes = [
        OrderShip::PAYMENT_TYPE_SENDER => GHTKShipper::PAYMENT_TYPE_SENDER,
        OrderShip::PAYMENT_TYPE_RECEIVER => GHTKShipper::PAYMENT_TYPE_RECEIVER
    ];

    public function getProvinceName($code)
    {
        $province = Province::find()->where(["code" => $code])->select("full_name")->one();
        return $province->full_name ?? null;
    }

    public function getDistrictName($code)
    {
        $district = District::find()->where(["code" => $code])->select("full_name")->one();
        return $district->full_name ?? null;
    }

    public function getWardName($code)
    {
        $ward = Ward::find()->where(["code" => $code])->select("full_name")->one();
        return $ward->full_name ?? null;
    }

    public function paramsCalculateDelivery(): array
    {
        return [
            "pick_address" => $this->order_ship->sender_address,
            "pick_name" => $this->order_ship->sender_name,
            "pick_province" => $this->getProvinceName($this->order_ship->sender_province_id),
            "pick_district" => $this->getDistrictName($this->order_ship->sender_district_id),
            "pick_ward" => $this->getWardName($this->order_ship->sender_ward_id),
            "pick_email" => $this->order_ship->sender_email,
            "pick_money" => $this->order_ship->cod,
            "address" => $this->order_ship->receiver_address,
            "province" => $this->getProvinceName($this->order_ship->receiver_province_id),
            "district" => $this->getDistrictName($this->order_ship->receiver_district_id),
            "ward" => $this->getWardName($this->order_ship->receiver_ward_id),
            "weight" => $this->order_ship->weight,
            "value" => (float)$this->order_ship->value,
            "deliver_option" => "none",
            "transport" => self::TRANSPORT[$this->order_ship->transport] ?? self::TRANSPORT[2],
        ];
    }

    public function paramCreateOrders()
    {
        $order = Order::find()->where(["id" => $this->order_ship->order_id])->one();
        $orderItems = $order->orderItems;
        $items = [];
        foreach ($orderItems as $item) {
            $items[] = [
                "product_code" => $item->productVariant->code,
                "name" => $item->productVariant->name,
                "price" => intval($item->productVariant->unit_price),
                "weight" => $item->productVariant->weight,
                "quantity" => $item->quantity
            ];
        }
        $request = [
            "order" => [
                "id" => $order->code,
                "pick_name" => $this->order_ship->sender_name,
                "pick_money" => $this->paymentTypes[$this->order_ship->payment_type] === GHTKShipper::PAYMENT_TYPE_SENDER
                    ? (float)$this->order_ship->cod
                    : (float)$this->order_ship->cod,
                "pick_address" => $this->order_ship->sender_address,
                "pick_province" => $this->getProvinceName($this->order_ship->sender_province_id),
                "pick_district" => $this->getDistrictName($this->order_ship->sender_district_id),
                "pick_ward" => $this->getWardName($this->order_ship->sender_ward_id),
                "pick_tel" => $this->order_ship->sender_phone,
                "pick_email" => $this->order_ship->sender_email,
                "name" => $this->order_ship->receiver_name,
                "address" => $this->order_ship->receiver_address,
                "province" => $this->getProvinceName($this->order_ship->receiver_province_id),
                "district" => $this->getDistrictName($this->order_ship->receiver_district_id),
                "ward" => $this->getWardName($this->order_ship->receiver_ward_id),
                "hamlet" => "Khác",
                "tel" => $this->order_ship->receiver_phone,
                "note" => $this->shipper_note,
                "email" => $this->order_ship->receiver_email,
                "use_return_address" => 0,
                "is_freeship" => $this->paymentTypes[$this->order_ship->payment_type],
                "weight_option" => [
                    OrderShip::WEIGHT_GRAM => GHTKShipper::WEIGHT_GRAM,
                    OrderShip::WEIGHT_KG => GHTKShipper::WEIGHT_KG
                ][$this->order_ship->weight_option],
                "total_weight" => $this->order_ship->weight,
                "value" => (float)$this->order_ship->value,
                "tags" => $this->tags,
                "transport" => self::TRANSPORT[$this->order_ship->transport] ?? self::TRANSPORT[2],
            ],
            "products" => $items
        ];
        return $request;
    }

    public function getIsFreeShip()
    {
    }

    public function loadServiceExtras()
    {
        $serviceExtras = $this->order_ship->selectServiceExtras("value", "shipper_note", "payment_type", "tag");
        foreach ($serviceExtras as $serviceExtra) {
            $this->{$serviceExtra["code"]} = $serviceExtra["value"] ?? null;
        }
        $this->service_extras = $serviceExtras;
    }
}
