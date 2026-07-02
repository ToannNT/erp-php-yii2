<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\ProductVariant as BaseProductVariant;

class ProductVariant extends BaseProductVariant
{

    public function fields()
    {
        return [
            "id",
            "name",
            "barcode",
            "sku"
        ];
    }

    public function getProductInventories()
    {
        return parent::getProductInventories()
            ->andOnCondition(["id" => "product_variant_id"])
            ->addSelect(["unit_price", "sll_price", "quantity", "available", "committed", "on_way", "incoming"]);
    }
}
