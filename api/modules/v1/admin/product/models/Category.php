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
