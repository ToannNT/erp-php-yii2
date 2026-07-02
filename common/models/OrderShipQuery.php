<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderShip]].
 *
 * @see OrderShip
 */
class OrderShipQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function notCancelBillLading()
    {
        return $this->andWhere(["<>", "status", OrderShip::STATUS_CANCEL_BILL_LADING]);
    }

    /**
     * @inheritdoc
     * @return OrderShip[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderShip|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
