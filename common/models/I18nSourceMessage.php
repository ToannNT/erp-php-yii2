<?php

namespace common\models;

use Yii;
use \common\models\base\I18nSourceMessage as BaseI18nSourceMessage;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "i18n_source_message".
 */
class I18nSourceMessage extends BaseI18nSourceMessage
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
