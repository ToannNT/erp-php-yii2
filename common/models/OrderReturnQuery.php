<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderReturn]].
 *
 * @see OrderReturn
 */
class OrderReturnQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return OrderReturn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderReturn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
