<?php

namespace common\models;

use Yii;
use \common\models\base\Property as BaseProperty;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "property".
 */
class Property extends BaseProperty
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
