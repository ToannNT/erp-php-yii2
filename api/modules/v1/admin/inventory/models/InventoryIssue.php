<?php

namespace api\modules\v1\admin\inventory\models;

use Exception;
use common\behaviors\JsonBehavior;
use common\models\InventoryIssue as BaseInventoryIssue;
use yii\helpers\ArrayHelper;
use common\models\InventoryHistory;

class InventoryIssue extends BaseInventoryIssue
{

    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "type",
            "order_id",
            "status",
            "created_at",
            "updated_at",
            "deleted_at",
            "delivery_date",
            "received_date",
            "created_by" => function () {
                return !empty($this->createdBy) ? $this->createdBy->username : null;
            },
            "total_number",
            "note",
            "progress_status",
            "office_receive" => "officeReceive",
            "office" => "office",
            "office_receive_id",
            "office_id",
            "inventory_receive" => "inventoryReceive",
            "inventory" => "inventory",
            "inventory_id",
            "inventory_receive_id",
            "issue_items" => "inventoryIssueItems"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["progress_status"]
        ];
        return $behaviors;
    }

    /**
     *
     * @param string $action
     * @param int|null $available
     * @param InventoryIssueItem $item
     * @return void
     * @throws Exception
     */
    public function createHistory(string $action, $available, $item)
    {
        $quantity = $item->quantity;
        $office_id = $this->office_receive_id;
        $type = InventoryHistory::TYPE_INVENTORY_ISSUE;
        if (in_array($action, [
            InventoryHistory::ACTION_INVENTORY_ISSUE,
            InventoryHistory::ACTION_CANCEL_INVENTORY_RECEIPT
        ])) {
            $quantity = -$item->quantity;
            $office_id = $this->office_id;
        }
        InventoryHistory::create([
            "action" => $action,
            "change_quantity" => sprintf("%+d", $quantity),
            "inventory" => $available,
            "voucher_code" => $this->code,
            "created_by" => $this->created_by,
            "inventory_id" => $this->inventory_id,
            "office_id" => $office_id,
            "product_id" => $item->product_id,
            "product_variant_id" => $item->product_variant_id,
            "type" => $type
        ]);
    }

    public function addProgressStatus($status)
    {
        $progress_status = json_decode(json_encode($this->progress_status), true);
        $progress_status = ArrayHelper::merge($progress_status, [
            [
                "status" => $status,
                "date" => date("Y-m-d H:i:s")
            ]
        ]);
        $this->progress_status = $progress_status;
    }

    public function getInventoryReceive()
    {
        return parent::getInventoryReceive()->addSelect(["id", "name"]);
    }

    public function getOfficeReceive()
    {
        return parent::getOfficeReceive()->addSelect(["id", "name"]);
    }

    public function getInventory()
    {
        return parent::getInventory()->addSelect(["id", "name"]);
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }

    public function getInventoryIssueItems()
    {
        return $this->hasMany(InventoryIssueItem::class, ["inventory_issue_id" => "id"]);
    }
}
