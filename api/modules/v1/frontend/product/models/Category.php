<?php

namespace api\modules\v1\frontend\product\models;

use common\models\Category as BaseCategory;
use common\models\Product as BaseProduct;

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

    public function extraFields()
    {
        return [
            "latest_products" => "latestProducts",
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


    public function getLatestProducts()
    {
        return $this->hasMany(BaseProduct::class, ["category_id" => "id"])
            ->orderBy(["id" => SORT_DESC])
            ->limit(5)
            ->select(["id", "name", "slug", "unit_price", "category_id"]);
    }

    public function getFirstIcon()
    {
        return is_array($this->icon) ? current($this->icon) : null;
    }
}
