<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ProductMetum]].
 *
 * @see ProductMetum
 */
class ProductMetumQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return ProductMetum[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProductMetum|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
