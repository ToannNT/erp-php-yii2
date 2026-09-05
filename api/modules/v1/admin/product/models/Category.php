<?php

namespace api\modules\v1\admin\product\models;

use common\validators\IsArrayValidator;
use Yii;
use yii\behaviors\SluggableBehavior;
use common\models\Category as BaseCategory;

class Category extends BaseCategory
{

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($insert) {
            $this->owner_id = Yii::$app->user->getId();
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert && !$this->code) {
            $this->setFormatCode();
            $this->save(false);
        }
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "status",
            "code",
            "description",
            "parent_id",
            "icon",
            // Thứ tự hiển thị (số nhỏ hiện trước) và cờ đưa lên trang chủ.
            "priority",
            "show_on_home",
            // Danh mục con. Map tay ra array thay vì trả AR: fields() của con cũng có
            // `children` nên serialize AR sẽ đệ quy và sinh thêm query mỗi cấp.
            "children" => function () {
                return array_map(static function (self $child) {
                    return [
                        "id" => $child->id,
                        "name" => $child->name,
                        "slug" => $child->slug,
                        "code" => $child->code,
                        "icon" => $child->icon,
                        "parent_id" => (int) $child->parent_id,
                        "priority" => (int) $child->priority,
                        "show_on_home" => (int) $child->show_on_home,
                        "status" => (int) $child->status,
                    ];
                }, $this->children);
            },
            "brands" => function () {
                // Map thẳng ra array thay vì trả AR: Brand::fields() của module admin có field
                // `categories` (relation) nên serialize AR sẽ sinh thêm 1 query mỗi nhãn hiệu.
                return array_map(static function ($brand) {
                    return [
                        "id" => $brand->id,
                        "name" => $brand->name,
                        "icon" => $brand->icon,
                        "slug" => $brand->slug,
                    ];
                }, $this->batchBrands);
            },
            "created_at",
            "updated_at",
        ];
    }

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'code'], 'unique', 'filter' => [
                "!=", "status", Category::STATUS_DELETE
            ]],
            [["status"], "default", "value" => Category::STATUS_INACTIVE],
            [["icon"], "default", "value" => []],
            [["description"], "string"],
            [["status"], "in", "range" => [
                Category::STATUS_ACTIVE, Category::STATUS_INACTIVE
            ]],
            ["icon", "safe"],
            // rules() ở đây REPLACE hoàn toàn parent nên phải khai lại, không thì
            // `priority`/`show_on_home` không nằm trong safe attributes và load() bỏ qua.
            [["priority", "show_on_home"], "default", "value" => 0],
            [["priority"], "integer"],
            [["show_on_home"], "boolean"],
            // `parent_id` vốn nằm trong fields() nhưng KHÔNG có trong rules() nên trước đây
            // load() bỏ qua — gửi lên là mất im lặng. Validate cụ thể ở CategoryForm.
            [["parent_id"], "integer"],
            [["parent_id"], "default", "value" => null]
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
     * Danh mục con trực tiếp, sắp đúng thứ tự hiển thị như mọi danh sách khác.
     *
     * Hệ thống chốt tối đa 2 cấp (gốc + con) nên con của con luôn rỗng — validate ở
     * CategoryForm::validateParent().
     */
    public function getChildren()
    {
        return $this->hasMany(self::class, ["parent_id" => "id"])
            ->andOnCondition(["<>", "category.status", self::STATUS_DELETE])
            ->orderBy(["category.priority" => SORT_ASC, "category.id" => SORT_DESC]);
    }

    public function getCategoryBrands()
    {
        return $this->hasMany(\common\models\CategoryBrand::class, ["category_id" => "id"]);
    }

    /**
     * Danh sách nhãn hiệu của category (qua bảng nối `category_brand`).
     *
     * Đặt tên `batchBrands` để không trùng property `$brands` nhận input ở CategoryForm —
     * PHP ưu tiên property hơn magic getter nên trùng tên sẽ làm chết relation.
     */
    public function getBatchBrands()
    {
        return $this->hasMany(\common\models\Brand::class, ["id" => "brand_id"])
            ->via("categoryBrands")
            ->select(["id", "name", "icon", "slug"]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["slug"] =
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
            ];
        return $behaviors;
    }
}
