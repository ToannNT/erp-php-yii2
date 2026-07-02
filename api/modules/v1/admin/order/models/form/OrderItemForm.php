<?php

namespace api\modules\v1\admin\order\models\form;

use api\modules\v1\admin\order\models\OrderItem;
use common\models\Order;
use common\models\ProductInventory;
use common\models\ProductVariant;

class OrderItemForm extends OrderItem
{
    public Order $order;

    public function rules(): array
    {
        return [
            [["order_id", "product_id", "product_variant_id", "quantity"], "required"],
            [["total_price"], "number", "min" => 0],
            [["note"], "string"],
            ["quantity", "integer", "min" => 1],
            ["product_variant_id", "productVariantValidator"],
            ["product_variant_id", "productInventoryValidator"]
        ];
    }

    public function productVariantValidator($attribute): bool
    {
        $product = ProductVariant::find()
            ->where(["product_id" => $this->product_id])
            ->andWhere(["id" => $this->product_variant_id])
            ->unDelete()
            ->one();
        if (!$product) {
            $this->addError($attribute, "Product Variant not found");
            return false;
        }
        $this->unit_price = $product->unit_price;
        if ($this->order->price_policy == Order::SLL_PRICE) {
            $this->unit_price = $product->sll_price;
        }
        return true;
    }


    public function productInventoryValidator($attribute): bool
    {
        $productInventory = ProductInventory::find()
            ->where(["product_variant_id" => $this->product_variant_id])
            ->andWhere(["inventory_id" => $this->order->inventory_id])
            ->one();
        if (!$productInventory) {
            $this->addError($attribute, "Product Inventory not found");
            return false;
        }
        if ($this->quantity > $productInventory->available) {
            $this->addError($attribute, "Quantity Product Inventory invalid, only accept <= {$productInventory->available}");
            return false;
        }
        return true;
    }
}
