<?php

namespace api\modules\v1\admin\person\models;

use Yii;
use common\models\CustomerNote as BaseCustomerNote;

class CustomerNote extends BaseCustomerNote
{
    public function fields()
    {
        return [
            "id",
            "note",
            "customer_name" => function ($model) {
                return $model->customer->name;
            },
            "created_by"    => function ($model) {
                return $model->createdBy->username;
            },
            "created_at",
            "updated_at"
        ];
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($insert) {
            $this->created_by = Yii::$app->user->getId();
        }
        return true;
    }

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [["customer_id", "note"], "required"],
            ["status", "default", "value" => CustomerNote::STATUS_ACTIVE],
            ["customer_id", "exist", "targetClass" => Customer::class, "targetAttribute" => ["customer_id" => "id"], "filter" => [
                "!=", "status", Customer::STATUS_DELETE
            ]]
        ];
    }
}
