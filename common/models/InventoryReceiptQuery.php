<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[InventoryReceipt]].
 *
 * @see InventoryReceipt
 */
class InventoryReceiptQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => InventoryReceipt::STATUS_ACTIVE]);
    }

    public function notDone()
    {
        return $this->andWhere(['<>', 'status', InventoryReceipt::RECEIPT_STATUS_DONE]);
    }

    public function notWareHouse()
    {
        return $this->andWhere(['<>', 'status', InventoryReceipt::RECEIPT_STATUS_WAREHOUSE]);
    }

    public function withoutCancel()
    {
        return $this->andWhere(["<>", 'status', InventoryReceipt::RECEIPT_STATUS_CANCEL]);
    }

    /**
     * @inheritdoc
     * @return InventoryReceipt[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InventoryReceipt|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
