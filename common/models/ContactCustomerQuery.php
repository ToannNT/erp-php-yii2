<?php

namespace common\models;

use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[ContactCustomer]].
 *
 * @see ContactCustomer
 */
class ContactCustomerQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function notDelete()
    {
        return $this->andWhere(['contact_customer.status' => null]);
    }


    /**
     * @inheritdoc
     * @return ContactCustomer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactCustomer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
