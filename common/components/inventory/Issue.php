<?php

namespace common\components\inventory;

use common\models\InventoryIssue;
use common\models\InventoryIssueItem;
use yii\base\Component;

class Issue extends Component
{

    protected $office_id;
    protected $inventory_id;
    protected $product_variant_id;
    protected $total_number;
    protected $order_id;
    protected $issue_id;

    public function setOrderId($id)
    {
        $this->order_id = $id;
    }

    public function setOfficeId($id)
    {
        $this->office_id = $id;
    }

    public function setInventoryId($id)
    {
        $this->inventory_id = $id;
    }

    public function setProductVariantId($id)
    {
        $this->product_variant_id = $id;
    }

    public function setTotalNumber($totalNumber)
    {
        $this->total_number = $totalNumber;
    }

    public function getIssueId()
    {
        return $this->issue_id;
    }

    public function create()
    {
        $inventoryIssue = new InventoryIssue([
            "office_id" => $this->office_id,
            "inventory_id" => $this->inventory_id,
            "total_number" => $this->total_number,
            "type" => InventoryIssue::TYPE_DELIVER,
            "order_id" => $this->order_id,
            "status" => InventoryIssue::STATUS_DONE
        ]);
        $inventoryIssue->save(false);
        $inventoryIssue->setFormatCode();
        $inventoryIssue->save(false);
        $this->issue_id = $inventoryIssue->id;
    }

    public function appendItem($issue_id, $product_variant_id, $quantity)
    {
        $inventoryIssueItem = new InventoryIssueItem([
            "inventory_issue_id"  => $issue_id,
            "product_variant_id"  => $product_variant_id,
            "quantity"            => $quantity
        ]);
        $inventoryIssueItem->save(false);
    }
}
