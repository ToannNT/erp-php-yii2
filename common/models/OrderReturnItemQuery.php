<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderReturnItem]].
 *
 * @see OrderReturnItem
 */
class OrderReturnItemQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return OrderReturnItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderReturnItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
