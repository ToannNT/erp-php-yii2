<?php

namespace api\modules\v1\admin\setting\models;

use common\models\Group;
use common\models\Supplier as BaseSupplier;

class Supplier extends BaseSupplier
{
    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "description",
            "contact" => "contact",
            "phone",
            "email",
            "address_1",
            "address_2",
            "note",
            "website",
            "fax",
            "tax_code",
            "groups" => "groups",
            "status",
            "created_at",
            "updated_at",
            "deleted_at"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getGroups()
    {
        return array_map(function ($group) {
            return $group->name;
        }, Group::find()->where(["id" => json_decode($this->group_id)])->addSelect(["name"])->all());
    }

    public function getContact()
    {
        return parent::getContact()->addSelect(["id", "name"]);
    }
}
