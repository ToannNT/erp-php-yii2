<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[InventoryHistory]].
 *
 * @see InventoryHistory
 */
class InventoryHistoryQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return InventoryHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InventoryHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
