<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderPromotion]].
 *
 * @see OrderPromotion
 */
class OrderPromotionQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return OrderPromotion[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderPromotion|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
