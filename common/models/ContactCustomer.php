<?php

namespace common\models;

use Yii;
use \common\models\base\ContactCustomer as BaseContactCustomer;
use yii\helpers\ArrayHelper;

/**
 * Class ContactCustomer
 * @property Contact $contact
 * @property Customer $customer
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class ContactCustomer extends BaseContactCustomer
{
    const STATUS_DELETE = -99;
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
