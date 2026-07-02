<?php

namespace common\models;

use Yii;
use \common\models\base\City as BaseCity;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cities".
 */
class City extends BaseCity
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
