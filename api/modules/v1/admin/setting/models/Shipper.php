<?php

namespace api\modules\v1\admin\setting\models;

use common\validators\IsArrayValidator;
use Yii;
use common\models\base\ActiveRecord;
use common\models\Shipper as BaseShipper;
use common\behaviors\JsonBehavior;
use yii\behaviors\SluggableBehavior;

class Shipper extends BaseShipper
{

    public function fields()
    {
        return [
            "id",
            "type",
            "name",
            "short_name",
            "slug",
            "thumbnail",
            "token",
            "created_by" => "createdBy",
            "service_extras",
            "created_at",
            "updated_at"
        ];
    }

    public function getCreatedBy()
    {
        return parent::getCreatedBy()->addSelect(["id", "username"]);
    }

    public function formName(): string
    {
        return "";
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["service_extras", "type"]
        ];
        $behaviors["slug"] = [
            'class' => SluggableBehavior::class,
            "attribute" => "name",
            'slugAttribute' => 'slug',
        ];
        return $behaviors;
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->created_by = Yii::$app->user->identity->getId();
        return true;
    }

    public function rules()
    {
        return [
            [["name", "short_name", "slug", "thumbnail", "token"], "string"],
            [["type", "service_extras"], IsArrayValidator::class],
            [["status"], "default", "value" => ActiveRecord::STATUS_ACTIVE]
        ];
    }
}
