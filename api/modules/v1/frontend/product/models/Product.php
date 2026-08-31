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
            "images",
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
                "jsonAttributes" => ["product_options", "additional_data", "tags", "images"]
            ]
        ]);
    }

    public function getProductVariants($selects = [])
    {
        // Không lọc thì biến thể đã xoá mềm vẫn hiện trên web khách, kể cả sau khi sản phẩm bị xoá.
        return $this->hasMany(ProductVariant::class, ["product_id" => "id"])
            ->andOnCondition(["<>", "product_variant.status", ProductVariant::STATUS_DELETE]);
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
