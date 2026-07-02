<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\ProductInventory as BaseProductInventory;

class ProductInventory extends BaseProductInventory
{
    public function fields()
    {
        return [
            "product_variant" => "productVariant",
            "quantity",
            "available",
            "incoming",
            "on_way",
            "committed",
            "unit_price",
            "sll_price",
            "created_at",
            "updated_at"
        ];
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect(["id", "name", "sku", "barcode"]);
    }
}
