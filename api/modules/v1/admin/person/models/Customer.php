<?php

namespace api\modules\v1\admin\person\models;

use common\models\District;
use common\models\Province;
use common\models\Ward;
use common\behaviors\JsonBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\Customer as BaseCustomer;
use common\models\Group;
use common\validators\IsArrayValidator;

class Customer extends BaseCustomer
{
    public function fields()
    {
        return [
            "id",
            "code",
            "name",
            "email",
            "phone",
            "status",
            "postal_code",
            "address_1",
            "address_2",
            "type",
            "note",
            "status",
            "groups" => "allGroups",
            "website",
            "province_code",
            "district_code",
            "ward_code",
            "created_at",
            "updated_at"
        ];
    }

    public function extraFields()
    {
        return [
            "count_contact" => "countContact",
            "count_note" => "countNote",
            "count_order" => "countOrder"
        ];
    }

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            "json" => [
                "class" => JsonBehavior::class,
                'jsonAttributes' => ["groups"]
            ],
            "softDeleteBehavior" => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'status' => Customer::STATUS_DELETE
                ],
            ]
        ]);
        return $behaviors;
    }

    public function getAllGroups()
    {
        return Group::find()->where(['id' => $this->groups])->addSelect(["id", "name"])->all();
    }

    public function rules()
    {
        return [
            [['name', 'phone', 'email'], 'required'],
            ['email', 'email'],
            [['groups'], IsArrayValidator::class],
            ['status', 'in', 'range' => [
                Customer::STATUS_ACTIVE,
                Customer::STATUS_INACTIVE
            ]],
            ['status', 'default', 'value' => Customer::STATUS_INACTIVE],
            ['type', 'in', 'range' => [
                Customer::TYPE_NEW,
                Customer::TYPE_ATRISK,
                Customer::TYPE_NORMAL,
                Customer::TYPE_VIP
            ]],
            [[
                'website', 'type', 'groups', 'note', 'status', 'postal_code', 'time_zone', 'address_1', 'address_2'
            ], 'safe'],
            ["province_code", "exist", "targetClass" => Province::class, "targetAttribute" => ["province_code" => "code"]],
            ["district_code", "exist", "targetClass" => District::class, "targetAttribute" => ["district_code" => "code"]],
            ["ward_code", "exist", "targetClass" => Ward::class, "targetAttribute" => ["ward_code" => "code"]],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $this->setFormatCode();
            return $this->save(false);
        }
    }

    public function formName()
    {
        return "";
    }
}
