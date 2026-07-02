<?php

namespace common\models;

use Yii;
use \common\models\base\CategoryBrand as BaseCategoryBrand;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category_brand".
 */
class CategoryBrand extends BaseCategoryBrand
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ["id" => "category_id"]);
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, ["id" => "brand_id"]);
    }

    public function formName()
    {
        return "";
    }
}
