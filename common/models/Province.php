<?php

namespace common\models;

use Yii;
use \common\models\base\Province as BaseProvince;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "province".
 */
class Province extends BaseProvince
{

    public function behaviors()
    {
        return [

        ];
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
