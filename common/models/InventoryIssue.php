<?php

namespace common\models;

use Yii;
use \common\models\base\InventoryIssue as BaseInventoryIssue;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class InventoryIssue
 * @property InventoryIssueItem[] $inventoryIssueItem
 * @property Office $office
 * @property Inventory $inventory
 * @property Office $officeReceive
 * @property Inventory $inventoryReceive
 * @property User $createdBy
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class InventoryIssue extends BaseInventoryIssue
{
    const STATUS_PENDING = 0;
    const STATUS_RECEIVE = 1;
    const STATUS_DONE = 2;
    const STATUS_DELETE = -99;
    const STATUS_CANCEL = -1;
    const TYPE_TRANSFER = 1;
    const TYPE_DELIVER = 2;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public static function doneOrder($order, $orderItems)
    {
        $inventoryIssue = new self([
            "office_id" => $order->office_id,
            "inventory_id" => $order->inventory_id,
            "note" => "Xuất kho cho đơn hàng",
            "created_by" => $order->created_by,
            "delivery_date" => date("Y-m-d H:i:s"),
            "received_date" => date("Y-m-d H:i:s"),
            "status" => self::STATUS_DONE,
            "type" => self::TYPE_DELIVER,
            "order_id" => $order->id
        ]);
        if (!$inventoryIssue->save()) {
            throw new Exception("Can't save Issue");
        }
        $inventoryIssue->generateOrderItems($orderItems);
        $inventoryIssue->setFormatCode();
        $inventoryIssue->save(false);
    }

    public function generateOrderItems($orderItems)
    {
        foreach ($orderItems as $item) {
            $model = new InventoryIssueItem();
            $model->load(ArrayHelper::toArray($item), "");
            $model->inventory_issue_id = $this->id;
            if (!$model->save()) {
                throw new Exception("Can't save Issue Item");
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryIssueItem()
    {
        return $this->hasMany(InventoryIssueItem::class, ['inventory_issue_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(Inventory::class, ['id' => 'inventory_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficeReceive()
    {
        return $this->hasOne(Office::class, ['id' => 'office_receive_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReceive()
    {
        return $this->hasOne(Inventory::class, ['id' => 'inventory_receive_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryIssueItems()
    {
        return $this->hasMany(InventoryIssueItem::class, ['inventory_issue_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    static function findAssignUser()
    {
        $offices = Office::getOfficeAssignUser();
        $query = self::find()->where(["in", "inventory_issue.office_id", array_column($offices, "id")]);
        return $query->active();
    }

    static function findInventoryIssueAssignUser($id)
    {
        $inventoryReceipt = self::findOne($id);
        $userOffices = Office::getOfficeAssignUser();
        $check = array_search($inventoryReceipt->office_id, array_column($userOffices, 'id'));
        return $check === false ? null : $inventoryReceipt;
    }

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'ISN' . $tmp;
    }

    public function types()
    {
        return [
            self::TYPE_TRANSFER => Yii::t("api", "Inventory Transfer"),
            self::TYPE_DELIVER => Yii::t("api", "Inventory Deliver")
        ];
    }

    public function getTypeText()
    {
        return [
            self::TYPE_TRANSFER => Yii::t("api", "Inventory Transfer"),
            self::TYPE_DELIVER => Yii::t("api", "Inventory Deliver")
        ][$this->type];
    }

    public function getTypeTextRaw()
    {
        if ($this->type == InventoryIssue::TYPE_DELIVER) {
            return '<span class="d-inline-block mw-120px btn-sm text-center text-white badge-packing">' . Yii::t("api", "Inventory Deliver") . '</span>';
        } else {
            return '<span class="d-inline-block mw-120px btn-sm text-center text-white badge-approval">' . Yii::t("api", "Inventory Transfer") . '</span>';
        }
    }

    public function getStatusText()
    {
        if ($this->status == InventoryIssue::STATUS_PENDING) {
            return Yii::t("api", "Pending");
        } else if ($this->status == InventoryIssue::STATUS_RECEIVE) {
            return Yii::t("api", "Receive");
        } else if ($this->status == InventoryIssue::STATUS_CANCEL) {
            return Yii::t("api", "Status Cancel");
        } else {
            return Yii::t("api", "Done");
        }
    }

    public function status()
    {
        return [
            InventoryReceipt::RECEIPT_STATUS_ORDER => Yii::t("api", "Order"),
            InventoryReceipt::RECEIPT_STATUS_APPROVAL => Yii::t("api", "Approval"),
            InventoryReceipt::RECEIPT_STATUS_WAREHOUSE => Yii::t("api", "Warehouse"),
            InventoryReceipt::RECEIPT_STATUS_DONE => Yii::t("api", "Done"),
            InventoryReceipt::RECEIPT_STATUS_CANCEL => Yii::t("api", "Status Cancel")
        ];
    }

    public function saveDone()
    {
        $this->status = InventoryIssue::STATUS_DONE;
        $this->received_date = date("Y-m-d H:i:s");
        return $this->save(false);
    }

    public function saveDelivery()
    {
        $this->delivery_date = date("Y-m-d H:i:s");
        $this->status = InventoryIssue::STATUS_RECEIVE;
        return $this->save(false);
    }
}
