<?php

namespace common\models;

use Yii;
use \common\models\base\DeliveryFee as BaseDeliveryFee;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "delivery_fee".
 */
class DeliveryFee extends BaseDeliveryFee
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
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

    public function softDelete()
    {
        $this->status = DeliveryFee::STATUS_DELETE;
        return $this->save(false);
    }
}
