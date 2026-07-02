<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Property]].
 *
 * @see Property
 */
class PropertyQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Property[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Property|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
