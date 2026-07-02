<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ProductSupplier]].
 *
 * @see ProductSupplier
 */
class ProductSupplierQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return ProductSupplier[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProductSupplier|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
