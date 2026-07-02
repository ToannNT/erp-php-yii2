<?php

namespace api\modules\v1\admin\product\models;

use common\models\CategoryBrand as BaseCategoryBrand;

class CategoryBrand extends BaseCategoryBrand
{
    public function fields()
    {
        return [
            "id",
            "category_id",
            "brand_id",
            "category" => "category",
            "brand" => "brand"
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ["id" => "category_id"]);
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, ["id" => "brand_id"]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [["category_id", 'brand_id'], "required"],
            [["category_id"], "exist", 'targetClass' => Category::class, "filter" => [
                "!=", "status", Category::STATUS_DELETE
            ], 'targetAttribute' => ['category_id' => 'id']],
            [["brand_id"], "exist", 'targetClass' => Brand::class, "filter" => [
                "!=", "status", Brand::STATUS_DELETE
            ], 'targetAttribute' => ['brand_id' => 'id']],
            ["status", "default", "value" => CategoryBrand::STATUS_ACTIVE]
        ]);
    }
}