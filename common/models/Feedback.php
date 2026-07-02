<?php

namespace common\models;

use Yii;
use \common\models\base\Feedback as BaseFeedback;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "feedback".
 */
class Feedback extends BaseFeedback
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
