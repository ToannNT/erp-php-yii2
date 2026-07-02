<?php

namespace api\modules\v1\frontend\pos\models;

use api\modules\v1\admin\product\models\InventoryHistory;
use Exception;
use Yii;

class InventoryIssueItem extends \common\models\InventoryIssueItem
{
    /**
     * @var InventoryIssue $inventoryIssue
     */
    public $inventoryIssue;

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            return false;
        }

        $inventoryHistory = new InventoryHistory([
            "action" => InventoryHistory::ACTION_INVENTORY_ISSUE_FOR_POS,
            "change_quantity" => -$this->quantity,
            "inventory" => $this->number_inventory,
            "inventory_id" => $this->inventoryIssue->inventory_id,
            "product_variant_id" => $this->product_variant_id,
            "voucher_code" => $this->inventoryIssue->code,
            "type" => InventoryHistory::TYPE_INVENTORY_ISSUE,
            "created_by" => Yii::$app->user->getId()
        ]);
        $inventoryHistory->save(false);
    }
}
