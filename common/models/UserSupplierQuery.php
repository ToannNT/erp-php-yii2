<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserSupplier]].
 *
 * @see UserSupplier
 */
class UserSupplierQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return UserSupplier[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserSupplier|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
