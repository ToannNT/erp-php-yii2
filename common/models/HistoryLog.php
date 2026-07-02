<?php

namespace common\models;

use Yii;
use \common\models\base\HistoryLog as BaseHistoryLog;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "history_log".
 */
class HistoryLog extends BaseHistoryLog
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
