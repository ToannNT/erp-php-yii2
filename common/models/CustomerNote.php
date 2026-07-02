<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerNote as BaseCustomerNote;
use yii\helpers\ArrayHelper;

/**
 * Class CustomerNote
 * @property User $createdBy
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class CustomerNote extends BaseCustomerNote
{

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
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ["id" => "customer_id"]);
    }
}
