<?php

namespace api\modules\v1\admin\order\models;

use common\models\ProductInventory;
use common\behaviors\JsonBehavior;
use common\models\OrderItem as BaseOrderItem;

class OrderItem extends BaseOrderItem
{

    public function fields()
    {
        return [
            "id",
            "order_id",
            "product_id",
            "product_variant_id",
            "product_variant" => "productVariant",
            "number_inventory_current" => function () {
                return $this->productInventory->available;
            },
            "number_inventory",
            "unit_price",
            "discount_price",
            "sub_total",
            "quantity",
            "quantity_return",
            "total_price",
            "created_at",
            "updated_at",
            "deleted_at"
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["data_tax"]
        ];
        return $behaviors;
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect(["id", "name", "sku"]);
    }

    public function getProductInventory()
    {
        return $this->hasOne(ProductInventory::className(), [
            "product_variant_id" => "product_variant_id"
        ]);
    }

    public function formName()
    {
        return "";
    }
}
