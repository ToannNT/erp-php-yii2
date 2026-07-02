<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\InventoryIssueItem as BaseInventoryIssueItem;

class InventoryIssueItem extends BaseInventoryIssueItem
{
    public function fields()
    {
        return [
            "id",
            "product_variant" => "productVariant",
            "product_id",
            "product_variant_id",
            "number_inventory",
            "quantity",
            "created_at",
            "updated_at",
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
