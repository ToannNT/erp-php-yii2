<?php

namespace api\modules\v1\admin\product\models;

use yii\behaviors\SluggableBehavior;
use common\models\Brand as BrandBase;
use Yii;

class Brand extends BrandBase
{
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($insert && Yii::$app->user) {
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
            "code",
            "categories" => function () {
                return $this->batchCategories;
            },
            "description",
            "slug",
            "status",
            "icon",
            "created_at",
            "updated_at",
        ];
    }

    public function formName()
    {
        return "";
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

    public function getCategoryBrands()
    {
        return $this->hasMany(\common\models\CategoryBrand::class, ["brand_id" => "id"]);
    }

    public function getBatchCategories()
    {
        return $this->hasMany(\common\models\Category::class, ["id" => "category_id"])
            ->via("categoryBrands")
            ->select(["id", "name", "icon", "slug"]);
    }
}
