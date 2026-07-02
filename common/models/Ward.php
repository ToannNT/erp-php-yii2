<?php

namespace common\models;

use Yii;
use \common\models\base\Ward as BaseWard;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ward".
 */
class Ward extends BaseWard
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
