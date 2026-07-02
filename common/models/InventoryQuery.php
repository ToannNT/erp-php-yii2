<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Inventory]].
 *
 * @see Inventory
 */
class InventoryQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['inventory.status' => Inventory::STATUS_ACTIVE]);
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(['<>', 'inventory.status', Inventory::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Inventory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Inventory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
