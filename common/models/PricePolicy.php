<?php

namespace common\models;

use Yii;
use \common\models\base\PricePolicy as BasePricePolicy;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "price_policy".
 */
class PricePolicy extends BasePricePolicy
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
}
