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
            "description",
            // Thứ tự hiển thị và cờ danh mục nổi bật trên trang chủ.
            "priority",
            "show_on_home",
            "parent_id",
            // Danh mục con. Map tay ra array thay vì trả AR: fields() của con cũng có
            // `children` nên serialize AR sẽ đệ quy và sinh thêm query mỗi cấp.
            "children" => function () {
                return array_map(static function (self $child) {
                    return [
                        "id" => $child->id,
                        "name" => $child->name,
                        "slug" => $child->slug,
                        "code" => $child->code,
                        "icon" => $child->getFirstIcon(),
                        "description" => $child->description,
                        "parent_id" => (int) $child->parent_id,
                        "priority" => (int) $child->priority,
                        "show_on_home" => (int) $child->show_on_home,
                    ];
                }, $this->children);
            },
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

    /**
     * Danh mục cha. NULL nghĩa là danh mục gốc (cấp 1).
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ["id" => "parent_id"]);
    }

    /**
     * Danh mục con trực tiếp — chỉ lấy cái đang hoạt động, sắp đúng thứ tự hiển thị.
     *
     * Khác admin: web khách chỉ được thấy `status = ACTIVE`, không phải "chưa xoá".
     */
    public function getChildren()
    {
        return $this->hasMany(self::class, ["parent_id" => "id"])
            ->andOnCondition(["category.status" => self::STATUS_ACTIVE])
            ->orderBy(["category.priority" => SORT_ASC, "category.id" => SORT_DESC]);
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
