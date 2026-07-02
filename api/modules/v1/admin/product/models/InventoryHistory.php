<?php

namespace api\modules\v1\admin\product\models;

use common\models\InventoryHistory as BaseInventoryHistory;

class InventoryHistory extends BaseInventoryHistory
{

    public function fields()
    {
        return [
            "id",
            "action",
            "change_quantity",
            "voucher_code",
            "link_detail",
            "created_by" => function () {
                return $this->createdBy->username;
            },
            "num_inventory" => function ($model) {
                return $model->inventory;
            },
            "inventory" => "modelInventory",
            "product_variant"   => "productVariant",
            "type",
            "created_at",
            "updated_at"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getModelInventory()
    {
        return parent::getModelInventory()->addSelect(["id", "name"]);
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect(["id", "name"]);
    }
}
