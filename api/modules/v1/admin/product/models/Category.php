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
            ["icon", "safe"]
        ];
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
