<?php

namespace api\modules\v1\admin\report\models;

use common\models\OrderItem;

class SaleOrderSearchProductVariant extends OrderItem
{
    public $bought;
    public $payment_after_return;
    public $payment_before_return;
    public $sum_sub_total;
    public $total_discount_price;
    public $total_price_return;
    public $total_quantity_return;
    public $product_variant_name;
    public $product_variant_sku;

    public function fields()
    {
        $total_price_return = $this->getOrderReturnItems()->sum("total_price");
        return [
            "product_variant" => "productVariant",
            "bought",
            "total_quantity_return" => function () {
                return $this->total_quantity_return ?: 0;
            },
            "total_price_return" => function () use ($total_price_return) {
                return $total_price_return;
            },
            "sum_sub_total",
            "total_discount_price",
            "payment_before_return",
            "payment_after_return" => function () use ($total_price_return) {
                return $this->payment_before_return - $total_price_return;
            },
            "suppliers" => "suppliers",
            "created_at",
            "updated_at"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getSuppliers(): \yii\db\ActiveQuery
    {
        return parent::getSuppliers()->addSelect(["id", "name"]);
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect(["id", "name", "sku", "code"]);
    }

}