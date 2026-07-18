<?php

namespace api\modules\v1\frontend\product\models;

use api\modules\v1\frontend\product\models\ProductVariant;
use common\behaviors\JsonBehavior;
use common\models\Product as BaseProduct;
use common\models\ProductCategory;

class Product extends BaseProduct
{

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "unit_price",
            "compare_price",
            "brand" => "brand",
            "category" => "category",
            "product_options",
            "specifications",
            "additional_data",
            "description",
            "short_description",
            "warranty_description",
            "allow_sell",
            "variants" => "productVariants",
            "additional_data"
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                "class" => JsonBehavior::class,
                "jsonAttributes" => ["product_options", "additional_data", "tags"]
            ]
        ]);
    }

    public function getProductVariants($selects = [])
    {
        return $this->hasMany(ProductVariant::class, ["product_id" => "id"]);
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, ["id" => "brand_id"]);
    }

    public function getCategory($selects = [])
    {
        return $this->hasOne(\common\models\Category::class, ["id" => "category_id"])->select(["id", "name", "slug"]);
    }
}
