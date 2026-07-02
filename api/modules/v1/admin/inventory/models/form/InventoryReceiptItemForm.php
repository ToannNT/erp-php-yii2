<?php

namespace api\modules\v1\admin\inventory\models\form;

use api\modules\v1\admin\inventory\models\InventoryReceipt;
use api\modules\v1\admin\inventory\models\InventoryReceiptItem;
use common\models\ProductVariant;

class InventoryReceiptItemForm extends InventoryReceiptItem
{
    public $receipt;

    public function rules()
    {
        return [
            [["receipt_id", "product_id", "product_variant_id", "quantity", "unit_price", "sub_total_price", "total_price"], "required"],
            ["discount_value", "required", "when" => function () {
                return !empty($this->discount_type);
            }],
            [["product_variant_id"], "productVariantValidator"],
            [["quantity"], "integer", "min" => 1],
            [["unit_price", "total_discount_value"], "number"],
            [["total_price", "tax_price"], "number"],
            [["discount_type"], "in", "range" => [
                InventoryReceipt::DISCOUNT_PERCENT,
                InventoryReceipt::DISCOUNT_PRICE
            ]],
            [["status"], "in", "range" => [
                InventoryReceiptItem::STATUS_ACTIVE,
                InventoryReceipt::STATUS_INACTIVE
            ]],
            [["note"], "string"],
//            ["total_price", "totalPriceValidator"]
        ];
    }

    public function totalPriceValidator($attribute)
    {
        $discountItem = 0;
        if ($this->discount_type == InventoryReceipt::DISCOUNT_PERCENT) {
            $discountItem = ($this->discount_value / 100) * $this->unit_price;
        } else if ($this->discount_type == InventoryReceipt::DISCOUNT_PRICE) {
            $discountItem = $this->discount_value;
        }
        $discountPrice = $discountItem * $this->quantity;
        $subTotal = $this->unit_price * $this->quantity - $discountPrice;
        // $taxPrice = $subTotal * env("TAX", 0.1);
        $taxPrice = 0;
        $totalPrice = $subTotal + $taxPrice;
        if ($totalPrice != $this->total_price) {
            $this->addError($attribute, "Invalid, only accept:" . $totalPrice);
        }
    }

    public function productVariantValidator($attribute)
    {
        $product = ProductVariant::find()
            ->where(["product_id" => $this->product_id])
            ->andWhere(["id" => $this->product_variant_id])
            ->unDelete()
            ->one();
        if (!$product) {
            $this->addError($attribute, "Product invalid");
        }
    }
}
