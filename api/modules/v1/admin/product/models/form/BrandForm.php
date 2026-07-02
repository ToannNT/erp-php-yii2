<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\CategoryBrand;
use Yii;
use api\modules\v1\admin\product\models\Brand;
use common\validators\IsArrayValidator;

class BrandForm extends Brand
{
    public $categories;

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->owner_id = Yii::$app->user->getId();
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert && !$this->code) {
            $this->setFormatCode();
            $this->save(false);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function createOrDeleteCategory()
    {
        $categoriesOld = CategoryBrand::find()
            ->select(["category_id"])
            ->where(["brand_id" => $this->id])
            ->asArray()
            ->all();
        $categoriesOld = array_column($categoriesOld, "category_id");
        foreach (array_diff($this->categories, $categoriesOld) as $category) {
            (new CategoryBrand(["brand_id" => $this->id, "category_id" => $category, "status" => CategoryBrand::STATUS_ACTIVE]))->save(false);
        }
        CategoryBrand::deleteAll(["brand_id" => $this->id, "category_id" => array_diff($categoriesOld, $this->categories)]);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'code'], 'unique', 'filter' => [
                "!=", "status", Brand::STATUS_DELETE
            ]],
            [["status"], "default", "value" => Brand::STATUS_INACTIVE],
            [["type"], "default", "value" => Brand::TYPE_ITEM],
            [['description', 'name'], 'string'],
            [["status"], "in", "range" => [
                Brand::STATUS_ACTIVE, Brand::STATUS_INACTIVE
            ]],
            ["icon", "safe"],
            ["categories", IsArrayValidator::class]
        ];
    }
}