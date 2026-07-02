<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\Inventory as BaseInventory;
use api\modules\v1\admin\inventory\models\ProductVariant;

class Inventory extends BaseInventory
{

    public $product_variant_name;
    public $product_variant_sku;

    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "product_inventories" => function ($model) {
                return $this->productInventories;
            },
            "created_at",
            "updated_at"
        ];
    }


    public function getProductInventories()
    {
        return $this->hasMany(ProductInventory::className(), ["inventory_id" => "id"])
            ->joinWith("productVariants");
    }

    public function getProductVariants()
    {
        return $this->hasMany(ProductVariant::className(), ["id" => "product_variant_id"]);
    }
}
