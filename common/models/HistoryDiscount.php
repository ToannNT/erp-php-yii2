<?php

namespace common\models;

use Yii;
use \common\models\base\HistoryDiscount as BaseHistoryDiscount;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "history_discount".
 */
class HistoryDiscount extends BaseHistoryDiscount
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;

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
