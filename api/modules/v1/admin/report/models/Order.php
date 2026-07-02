<?php

namespace api\modules\v1\admin\report\models;

use common\models\Order as BaseOrder;

class Order extends BaseOrder
{
    public $sum_quantity;
    public $sum_total_price;
    public $sum_discount;
    public $sum_delivery_fee;
    public $sum_payments;
    public $total_discount_product;
    public $total_price_return;
    public $payments_after_return;

    public function fields()
    {
        return [
            "id",
            "code",
            "channel",
            "quantity",
            "type",
            "discount",
            "sum_discount_product" => function () {
                return (float)$this->sumDiscountPriceOrderItems;
            },
            "sum_sub_total_product" => function () {
                return (float)$this->sumSubTotalOrderItems;
            },
            "total_price",
            "tax_price",
            "delivery_fee" => function () {
                return $this->delivery_fee + $this->other_fee;
            },
            "data_delivery_fee",
            "status",
            "created_at",
            "updated_at",
            "total_price_return" => 'totalPriceReturn',
            "paymentMethods" => "objectPaymentMethods",
            "payments_after_return" => function () {
                return $this->sumSubTotalOrderItems - $this->totalPriceReturn - $this->sumDiscountPriceOrderItems - $this->discount;
            },
        ];
    }

    public function getTotalPriceReturn()
    {
        $total_price_return = 0;
        foreach ($this->orderReturns as $orderReturn) {
            $total_price_return += $orderReturn->total_price;
        }
        return $total_price_return;
    }
}
