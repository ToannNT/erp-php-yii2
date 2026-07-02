<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Customer]].
 *
 * @see Customer
 */
class CustomerQuery extends \common\models\base\ActiveQuery
{
    public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }

    public function notDelete()
    {
        return $this->andWhere(['<>', 'customer.status', Customer::STATUS_DELETE]);
    }

    /**
     * @inheritdoc
     * @return Customer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Customer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
