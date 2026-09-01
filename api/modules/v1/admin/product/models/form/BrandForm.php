<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\CategoryBrand;
use Yii;
use api\modules\v1\admin\product\models\Brand;
use common\validators\IsArrayValidator;

class BrandForm extends Brand
{
    public $categories;

    public function createOrDeleteCategory()
    {
        // Không gửi `categories` => giữ nguyên; gửi [] => bỏ hết. Thiếu guard này thì array_diff(null) fatal ở PHP 8.
        if (!is_array($this->categories)) {
            return;
        }
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
            // rules() ở form này REPLACE hoàn toàn parent (không array_merge) nên phải khai lại,
            // không thì `priority`/`show_on_home` không nằm trong safe attributes và load() bỏ qua.
            [["priority", "show_on_home"], "default", "value" => 0],
            [["priority"], "integer"],
            [["show_on_home"], "boolean"],
            ["categories", IsArrayValidator::class]
        ];
    }
}