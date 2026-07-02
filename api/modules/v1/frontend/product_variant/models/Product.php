<?php

namespace api\modules\v1\frontend\product_variant\models;

use common\behaviors\JsonBehavior;
use common\models\Product as BaseProduct;

class Product extends BaseProduct
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                "class" => JsonBehavior::class,
                "jsonAttributes" => ["product_options"]
            ]
        ]);
    }
}