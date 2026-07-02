<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\ProductInventory;
use common\models\Inventory;

class InitProductInventory extends ProductInventory
{
    public $variant;
    public $inventory;

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->available            = $this->quantity;
        $this->product_id           = $this->variant->product_id;
        $this->product_variant_id   = $this->variant->id;
        return true;
    }

    public function rules()
    {
        return [
            [["unit_price", "sll_price", "quantity", "inventory_id"], "required"],
            [["unit_price", "sll_price", "quantity"], "number", "min" => 0],
            [["inventory_id"], "exist", 'targetClass' => Inventory::class, "filter" => [
                "=", "status", Inventory::STATUS_ACTIVE
            ], 'targetAttribute' => ['inventory_id' => 'id']],
        ];
    }

    public function initInventory()
    {
        return $this->save();
    }
}
