<?php

namespace common\models;

use Yii;
use \common\models\base\InventoryReceipt as BaseInventoryReceipt;
use yii\helpers\ArrayHelper;
use common\models\Office;


/**
 * Class InventoryReceipt
 * @property Office $office
 * @property Inventory $inventory
 * @property Supplier $supplier
 * @property user $createdBy
 * @property InventoryReceiptItem[] $inventoryReceiptItems
 * @property string $receiptStatusHtml
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class InventoryReceipt extends BaseInventoryReceipt
{

    const RECEIPT_STATUS_ORDER = 0;
    const RECEIPT_STATUS_APPROVAL = 1;
    const RECEIPT_STATUS_WAREHOUSE = 2;
    const RECEIPT_STATUS_DONE = 3;
    const RECEIPT_STATUS_CANCEL = -1;
    const DISCOUNT_PRICE = 2;
    const DISCOUNT_PERCENT = 1;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;

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

    public function attributeLabels()
    {
        return [
            "supplier_id"    => Yii::t("api", "Supplier"),
            "office_id"      => Yii::t("api", "Office"),
            "inventory_id"   => Yii::t("api","Inventory"),
            "delivery_date"  => Yii::t("api","Delivery Date"),
            "note"           => Yii::t("api","Note")
        ];
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
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getStatus()
    {
        switch ($this->status) {
            case self::RECEIPT_STATUS_ORDER:
                return Yii::t("api", "Pending Approval");
            case self::RECEIPT_STATUS_APPROVAL:
                return Yii::t("api", "Approved");
            case self::RECEIPT_STATUS_WAREHOUSE:
                return Yii::t("api", "Warehouse");
            case self::RECEIPT_STATUS_DONE:
                return Yii::t("api", 'Done');
            case self::RECEIPT_STATUS_CANCEL:
                return Yii::t("api", 'Status Cancel');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReceiptItems()
    {
        return $this->hasMany(InventoryReceiptItem::class, ['receipt_id' => 'id']);
    }

    static function getQueryAssignUser($id)
    {
        $inventoryReceipt = self::find();
        /* user permission supplier only your receipt */
        if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $inventoryReceipt->andWhere(["created_by" => Yii::$app->user->getId()]);
        }
        $inventoryReceipt = $inventoryReceipt->andWhere(["id" => $id]);
        $userOffices = Office::getOfficeAssignUser();
        $inventoryReceipt->andWhere(["office_id" => array_column($userOffices, 'id')]);
        return $inventoryReceipt;
    }

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'IRN' . $tmp;
    }
    
}
