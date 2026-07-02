<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ProductInventory]].
 *
 * @see ProductInventory
 */
class ProductInventoryQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return ProductInventory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProductInventory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
