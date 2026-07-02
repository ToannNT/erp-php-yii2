<?php

namespace common\base\cms\models;

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
            "unit_price",
            "meta_field",
            "images"
        ];
    }
}
