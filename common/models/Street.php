<?php

namespace common\models;

use Yii;
use \common\models\base\Street as BaseStreet;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "street".
 */
class Street extends BaseStreet
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
