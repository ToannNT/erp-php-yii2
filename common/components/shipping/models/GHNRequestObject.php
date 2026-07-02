<?php

namespace common\components\shipping\models;

use common\components\shipping\IShipper;
use common\components\shipping\shipper\GHNShipper;
use common\models\District;
use common\models\Order;
use common\models\OrderShip;
use common\models\Ward;

/**
 * @property OrderShip $orderShip
 * @property float $value
 * @property string $shipper_note
 */
class GHNRequestObject extends ShipperRequestBaseObject
{
    /**
     * @var OrderShip $order_ship
     */
    public $order_ship;
    public $value;
    public $shipper_note;
    public $payment_type;
    public $coupon;
    public $service_extras;

    protected $transports = [
        OrderShip::TRANSPORT_FLY => GHNShipper::FAST_DELIVERY_TRANSPORT,
        OrderShip::TRANSPORT_ROAD => GHNShipper::NORMAL_DELIVERY_TRANSPORT
    ];

    protected $paymentTypes = [
        OrderShip::PAYMENT_TYPE_SENDER => GHNShipper::PAYMENT_TYPE_SENDER,
        OrderShip::PAYMENT_TYPE_RECEIVER => GHNShipper::PAYMENT_TYPE_RECEIVER
    ];


    public function loadServiceExtras()
    {
        $serviceExtras = $this->order_ship->selectServiceExtras("value", "coupon", "payment_type", "shipper_note");
        foreach ($serviceExtras as $serviceExtra) {
            $this->{$serviceExtra["code"]} = $this->order_ship[$serviceExtra["code"]] ?? null;
        }
        $this->service_extras = $serviceExtras;
    }

    public function getDistrictCode($code): string
    {
        $district = District::find()->where(["code" => $code])->select("code_ghn")->one();
        return $district->code_ghn ?? "";
    }

    public function getWardCode($code): string
    {
        $ward = Ward::find()->where(["code" => $code])->select("code_ghn")->one();
        return $ward->code_ghn ?? "";
    }

    public function getService($transport)
    {
        return $this->transports[$transport] ?? GHNShipper::STANDARD_DELIVERY_TRANSPORT;
    }

    // set default sender/shop payment fee
    public function getPaymentType($paymentType)
    {
        return GHNShipper::PAYMENT_TYPE_SENDER;
    }

    public function getParamCalculatorFees(): array
    {
        return [
            "to_district_id" => (int)$this->getDistrictCode($this->order_ship->receiver_district_id),
            "to_ward_code" => (int)$this->getWardCode($this->order_ship->receiver_ward_id),
            "from_district_id" => (int)$this->getDistrictCode($this->order_ship->sender_district_id),
            "height" => $this->order_ship->height,
            "length" => $this->order_ship->length,
            "weight" => $this->order_ship->weight,
            "width" => $this->order_ship->width,
            "insurance_value" => (float)$this->order_ship->value,
            "service_type_id" => $this->order_ship->transport,
            "coupon" => $this->coupon,
            "required_note" => $this->shipper_note
        ];
    }

    public function getParamCreateOrders(): array
    {
        $order = Order::find()->where(["id" => $this->order_ship->order->id])->one();
        $items = [];
        foreach ($order->orderItems as $orderItem) {
            $productVariant = $orderItem->productVariant;
            $items[] = [
                "name" => $productVariant->name,
                "code" => $productVariant->name,
                "quantity" => $orderItem->quantity,
                "price" => (int)$orderItem->unit_price,
                "weight " => $productVariant->weight
            ];
        }
        $request = [
            "client_order_code" => $order->code,
            "cod_amount" => $this->paymentTypes[$this->order_ship->payment_type] === GHNShipper::PAYMENT_TYPE_SENDER
                ? (float)$this->order_ship->cod
                : (int)$this->order_ship->cod,
            "to_district_id" => $this->getDistrictCode($this->order_ship->receiver_district_id),
            "to_ward_code" => $this->getWardCode($this->order_ship->receiver_ward_id),
            "to_name" => $this->order_ship->receiver_name,
            "to_phone" => $this->order_ship->receiver_phone,
            "to_address" => $this->order_ship->receiver_address,
            "required_note" => $this->shipper_note,
            "height" => $this->order_ship->height,
            "length" => $this->order_ship->length,
            "weight" => $this->order_ship->weight,
            "width" => $this->order_ship->width,
            "insurance_value" => (float) $this->order_ship->value,
            "service_type_id" => $this->order_ship->transport,
            "payment_type_id" => $this->paymentTypes[$this->order_ship->payment_type],
            "Items" => $items
        ];
        return $request;
    }
}