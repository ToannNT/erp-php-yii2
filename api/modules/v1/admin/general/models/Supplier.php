<?php

namespace api\modules\v1\admin\general\models;

use common\models\Supplier as BaseSupplier;

class Supplier extends BaseSupplier
{

    public function fields()
    {
        return [
            "id",
            "name",
            "email",
            "phone",
            "address_1"
        ];
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->status = BaseSupplier::STATUS_ACTIVE;
        return true;
    }

    public function rules()
    {
        return [
            [["name", "email", "phone", "address_1"], "required"],
            ["name", "unique", "filter" => [
                "!=", "status", BaseSupplier::STATUS_DELETE
            ]],
            ["email", "email"]
        ];
    }

    public function formName()
    {
        return "";
    }
}
