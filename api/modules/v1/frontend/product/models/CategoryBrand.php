<?php

namespace api\modules\v1\frontend\product\models;

use common\models\CategoryBrand as BaseCategoryBrand;

class CategoryBrand extends BaseCategoryBrand
{
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ["id" => "brand_id"]);
    }
}