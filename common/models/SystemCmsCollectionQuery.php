<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[SystemCmsCollection]].
 *
 * @see SystemCmsCollection
 */
class SystemCmsCollectionQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return SystemCmsCollection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SystemCmsCollection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
