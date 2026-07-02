<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderItem]].
 *
 * @see OrderItem
 */
class OrderItemQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function unDelete()
    {
        $this->andWhere(["<>", "status", OrderItem::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return OrderItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
