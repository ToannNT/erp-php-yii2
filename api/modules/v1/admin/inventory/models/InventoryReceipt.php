<?php

namespace api\modules\v1\admin\inventory\models;

use common\models\InventoryReceipt as BaseInventoryReceipt;
use common\behaviors\JsonBehavior;
use yii\helpers\ArrayHelper;

class InventoryReceipt extends BaseInventoryReceipt
{
    public function fields()
    {
        return [
            "id",
            "code",
            "status",
            "office" => "office",
            "office_id",
            "inventory" => "inventory",
            "inventory_id",
            "supplier" => "supplier",
            "quantity",
            "sub_total_price",
            "total_price",
            "total_discount_type",
            "total_discount_value",
            "receipt_items" => "inventoryReceiptItems",
            "billing_address",
            "shipping_address",
            "progress_status",
            "other_cost",
            "tags",
            "user" => "createdBy",
            "note",
            "delivery_date",
            "created_at",
            "updated_at"
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["billing_address", "shipping_address", "other_cost", "tags", "progress_status"]
        ];
        return $behaviors;
    }

    public function formName()
    {
        return "";
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

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getInventory()
    {
        return parent::getInventory()->addSelect(["id", "name"]);
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }

    public function getSupplier()
    {
        return parent::getSupplier()->addSelect(["id", "name"]);
    }

    public function getInventoryReceiptItems()
    {
        return $this->hasMany(InventoryReceiptItem::className(), ["receipt_id" => "id"]);
    }

    public function getCreatedBy()
    {
        return parent::getCreatedBy()->addSelect(["id", "username"]);
    }
}
