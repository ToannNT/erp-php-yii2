<?php

namespace api\modules\v1\frontend\product_variant\models;

use common\behaviors\JsonBehavior;
use common\models\ProductVariant as BaseProductVariant;

class ProductVariant extends BaseProductVariant
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                "class" => JsonBehavior::class,
                "jsonAttributes" => ["meta_field", "images", "extra_fields"]
            ]
        ]);
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "extra_fields",
            "meta_field",
            "unit_price",
            "images",
            "product_options" => "productOption"
        ];
    }

    public function getProduct($selects = [])
    {
        return $this->hasOne(Product::class, ["id" => "product_id"]);
    }

    public function getProductOption()
    {
        return !empty($this->product) ? $this->product->product_options : null;
    }
}