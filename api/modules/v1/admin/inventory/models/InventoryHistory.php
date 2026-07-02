<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\InventoryHistory as BaseInventoryHistory;

class InventoryHistory extends BaseInventoryHistory
{

    public function fields()
    {
        return [
            "id",
            "created_by" => function () {
                return empty($this->createdBy) ?: $this->createdBy->username;
            },
            "product_variant_id",
            "product_id",
            "action",
            "change_quantity",
            "number_inventory" => function () {
                return $this->inventory;
            },
            "inventory" => function () {
                return $this->modelInventory;
            },
            "office" => function () {
                return $this->modelOffice;
            },
            "voucher_code",
            "product_variant" => "productVariant",
            "type",
            "created_at",
            "updated_at",
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect(["id", "name", "sku"]);
    }

    public function getModelInventory()
    {
        return parent::getModelInventory()->addSelect(["id", "name", "office_id"]);
    }

    public function getModelOffice()
    {
        return parent::getModelOffice()->addSelect(["id", "name"]);
    }

}