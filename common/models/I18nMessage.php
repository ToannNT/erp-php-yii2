<?php

namespace common\models;

use Yii;
use \common\models\base\I18nMessage as BaseI18nMessage;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "i18n_message".
 */
class I18nMessage extends BaseI18nMessage
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
