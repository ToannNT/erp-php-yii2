<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CmsProductPromotion]].
 *
 * @see CmsProductPromotion
 */
class CmsProductPromotionQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CmsProductPromotion[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CmsProductPromotion|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
