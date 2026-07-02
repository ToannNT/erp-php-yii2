<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ProductAsset]].
 *
 * @see ProductAsset
 */
class ProductAssetQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return ProductAsset[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProductAsset|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
