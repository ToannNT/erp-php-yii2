<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[StocktakingItem]].
 *
 * @see StocktakingItem
 */
class StocktakingItemQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return StocktakingItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StocktakingItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
