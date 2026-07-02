<?php

namespace api\modules\v1\admin\setting\models;

use common\models\Inventory as BaseInventory;

class Inventory extends BaseInventory
{
    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "description",
            "office",
            "owner" => "owner",
            "created_at",
            "updated_at",
            "deleted_at",
            "status"
        ];
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }

    public function getOwner()
    {
        return parent::getOwner()->addSelect(["id", "username"]);
    }

    public function rules()
    {
        return [
            [['name', 'office_id'], 'required'],
            [["office_id"], "exist", 'targetClass' => Office::class, 'targetAttribute' => ['office_id' => 'id'], 'filter' => [
                '=', 'status', Office::STATUS_ACTIVE
            ]],
            [['name'], 'unique', 'filter' => [
                '!=', 'status', Inventory::STATUS_DELETE
            ]],
            [['status'], 'default', 'value' => Inventory::STATUS_ACTIVE],
            ["owner_id", "exist", "targetClass" => User::class, 'targetAttribute' => ['owner_id' => 'id'], 'filter' => [
                '=', 'status', User::STATUS_ACTIVE
            ]],
            ["description", "string"]
        ];
    }
}
