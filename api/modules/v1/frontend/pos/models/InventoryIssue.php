<?php

namespace api\modules\v1\frontend\pos\models;

use Yii;

class InventoryIssue extends \common\models\InventoryIssue
{
    public $issueItems;
    public $number_inventory;

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            return false;
        }
        $this->setFormatCode();
        foreach ($this->issueItems as $issueItem) {
            $inventoryIssueItem = new InventoryIssueItem([
                "inventory_issue_id"  => $this->id,
                "product_variant_id"  => $issueItem["product_variant_id"],
                "number_inventory"       => $issueItem["number_inventory"],
                "quantity"            => $issueItem["quantity"]
            ]);
            $this->total_number += $issueItem["quantity"];
            $this->number_inventory += $issueItem["number_inventory"];
            $inventoryIssueItem->inventoryIssue = $this;
            $inventoryIssueItem->save();
        }
        $this->save(false);
    }
}
