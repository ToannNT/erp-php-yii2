<?php

namespace common\base\cms\models;

use common\behaviors\JsonBehavior;
use common\models\Product as BaseProduct;

class Product extends BaseProduct
{
    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "unit_price",
            "product_options",
            "variants" => "productVariants",
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                "class" => JsonBehavior::class,
                "jsonAttributes" => ["product_options"]
            ]
        ]);
    }

    public function getProductVariants($selects = [])
    {
        return $this->hasMany(ProductVariant::class, ["product_id" => "id"]);
    }
}