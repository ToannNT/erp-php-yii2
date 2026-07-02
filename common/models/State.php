<?php

namespace common\models;

use Yii;
use \common\models\base\State as BaseState;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "states".
 */
class State extends BaseState
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
