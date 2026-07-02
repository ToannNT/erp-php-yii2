<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderOrderReturn]].
 *
 * @see OrderOrderReturn
 */
class OrderOrderReturnQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return OrderOrderReturn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderOrderReturn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
