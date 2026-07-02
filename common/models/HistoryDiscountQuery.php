<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[HistoryDiscount]].
 *
 * @see HistoryDiscount
 */
class HistoryDiscountQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return HistoryDiscount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return HistoryDiscount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
