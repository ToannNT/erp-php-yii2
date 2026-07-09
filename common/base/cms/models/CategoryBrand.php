<?php

namespace common\base\cms\models;

use common\models\CategoryBrand as BaseCategoryBrand;

class CategoryBrand extends BaseCategoryBrand
{
    public function fields()
    {
        return [
            "id",
            "brand_id",
            "category_id",
            "status",
        ];
    }
}
