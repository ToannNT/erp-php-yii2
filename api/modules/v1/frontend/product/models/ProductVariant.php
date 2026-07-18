<?php

namespace api\modules\v1\frontend\product\models;

use common\behaviors\JsonBehavior;

class ProductVariant extends \common\models\ProductVariant
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
            "compare_price",
            "images"
        ];
    }
}
