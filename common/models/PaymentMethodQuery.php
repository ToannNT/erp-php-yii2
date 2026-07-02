<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[PaymentMethod]].
 *
 * @see PaymentMethod
 */
class PaymentMethodQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return PaymentMethod[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentMethod|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
