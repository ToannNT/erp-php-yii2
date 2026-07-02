<?php

namespace common\models;

use Yii;
use \common\models\base\Timezone as BaseTimezone;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "timezone".
 */
class Timezone extends BaseTimezone
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
