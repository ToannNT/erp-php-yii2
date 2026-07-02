<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderDiscount]].
 *
 * @see OrderDiscount
 */
class OrderDiscountQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return OrderDiscount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderDiscount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
