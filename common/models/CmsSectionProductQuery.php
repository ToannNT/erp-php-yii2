<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CmsSectionProduct]].
 *
 * @see CmsSectionProduct
 */
class CmsSectionProductQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CmsSectionProduct[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CmsSectionProduct|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
