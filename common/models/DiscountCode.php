<?php

namespace common\models;

use Yii;
use \common\models\base\DiscountCode as BaseDiscountCode;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "discount_code".
 */
class DiscountCode extends BaseDiscountCode
{
    const DISCOUNT_TYPE_PRICE = 1;
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
}
