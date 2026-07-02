<?php

namespace api\modules\v1\admin\person\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\ContactCustomer as BaseContactCustomer;

class ContactCustomer extends BaseContactCustomer
{

    public function fields()
    {
        return [
            "id",
            "contact" => "contact",
            "customer" => "customer",
            "status",
            "created_at",
            "updated_at",
        ];
    }

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            "softDeleteBehavior" => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'status' => Customer::STATUS_DELETE
                ],
            ]
        ]);
        return $behaviors;
    }

    public function getCustomer()
    {
        return parent::getCustomer()->addSelect(["id", "name"]);
    }

    public function getContact()
    {
        return parent::getContact()->addSelect(["id", "name", "email", "phone", "address_1"]);
    }

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [["contact_id", "customer_id"], "required"],
            ["contact_id", "exist", "targetClass" => Contact::class, "targetAttribute" => ["contact_id" => "id"], "filter" => [
                "!=", "status", Contact::STATUS_DELETE
            ]],
            ["customer_id", "exist", "targetClass" => Customer::class, "targetAttribute" => ["customer_id" => "id"], "filter" => [
                "!=", "status", Customer::STATUS_DELETE
            ]]
        ];
    }
}
