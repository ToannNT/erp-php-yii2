<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Stocktaking]].
 *
 * @see Stocktaking
 */
class StocktakingQuery extends \common\models\base\ActiveQuery
{
    public function active()
    {
        $this->andWhere(['!=', 'stocktaking.status', Stocktaking::STATUS_DELETE]);
        return $this;
    }

    public function pedding()
    {
        return $this->andWhere(["status" => Stocktaking::STATUS_PENDING]);
    }

    public function notDone()
    {
        return $this->andWhere(["<>", 'stocktaking.status', Stocktaking::STATUS_DONE]);
    }

    public function notCancel()
    {
        return $this->andWhere(["<>", 'stocktaking.status', Stocktaking::STATUS_CANCEL]);
    }

    /**
     * @inheritdoc
     * @return Stocktaking[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Stocktaking|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
