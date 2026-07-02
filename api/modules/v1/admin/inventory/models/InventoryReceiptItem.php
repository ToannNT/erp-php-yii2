<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\InventoryReceiptItem as BaseInventoryReceiptItem;

class InventoryReceiptItem extends BaseInventoryReceiptItem
{

    public function fields()
    {
        return [
            "id",
            "receipt_id",
            "product_id",
            "product_variant_id",
            "product_variant" => "productVariant",
            "quantity",
            "unit_price",
            "sub_total_price",
            "total_price",
            "discount_type",
            "discount_value",
            "total_discount_value",
            "tax_price",
            "created_at",
            "updated_at"
        ];
    }

    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ["id" => "product_variant_id"]);
    }

    public function formName()
    {
        return "";
    }
}
