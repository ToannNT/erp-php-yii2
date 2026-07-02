<?php

namespace api\modules\v1\frontend\pos\models\form;

class CustomerForm extends \common\models\Customer
{

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->setFormatCode();
            $this->status = self::STATUS_ACTIVE;
            $this->save(false);
        }
    }

    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['email'], 'email'],
            [['address_1', 'address_2', 'note', 'description'], 'string'],
            [['customer_phone', 'customer_email', 'groups'], 'safe']
        ];
    }
}
