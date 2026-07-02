<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[InventoryReceiptItem]].
 *
 * @see InventoryReceiptItem
 */
class InventoryReceiptItemQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return InventoryReceiptItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InventoryReceiptItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
