<?php

namespace api\modules\v1\frontend\product\models;

use common\models\Category as BaseCategory;

class Category extends BaseCategory
{
    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "icon" => "firstIcon",
            "created_at",
            "updated_at",
            "brands" => "brands",
            "status",
            "slug"
        ];
    }

    public function getCategoryBrand()
    {
        return $this->hasMany(CategoryBrand::class, ["category_id" => "id"]);
    }

    public function getBrands()
    {
        return $this->hasMany(Brand::class, ["id" => "brand_id"])->via("categoryBrand");
    }

    public function getFirstIcon()
    {
        return is_array($this->icon) ? current($this->icon) : null;
    }

}
