<?php

namespace common\models;

use Yii;
use \common\models\base\Room as BaseRoom;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "room".
 */
class Room extends BaseRoom
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

    public function formName()
    {
        return '';
    }
}
