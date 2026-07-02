<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\StocktakingItem as BasestocktakingItem;

class StocktakingItem extends BasestocktakingItem
{
    public function fields()
    {
        return [
            "id",
            "stocktaking_id",
            "product_variant" => "productVariant",
            "product_id",
            "product_variant_id",
            "number_inventory",
            "number_adjustment",
            "number_difference",
            "reason",
            "created_at"
        ];
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect(["id", "name", "slug", "sku"]);
    }

    public function formName()
    {
        return "";
    }
}
