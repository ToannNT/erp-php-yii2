<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CmsProductFeature]].
 *
 * @see CmsProductFeature
 */
class CmsProductFeatureQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CmsProductFeature[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CmsProductFeature|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
