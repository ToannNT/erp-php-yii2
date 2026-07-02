<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Shipper]].
 *
 * @see Shipper
 */
class ShipperQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function notDelete()
    {
        return $this->andWhere(["<>", "status", Shipper::STATUS_DELETE]);
    }

    /**
     * @inheritdoc
     * @return Shipper[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Shipper|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
