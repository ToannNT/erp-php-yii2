<?php

namespace common\models;

use Yii;
use \common\models\base\Country as BaseCountry;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "countries".
 */
class Country extends BaseCountry
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
