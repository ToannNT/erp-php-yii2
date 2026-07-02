<?php

namespace common\models;

use Yii;
use \common\models\base\CmsSliderTop as BaseCmsSliderTop;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_slider_tops".
 */
class CmsSliderTop extends BaseCmsSliderTop
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
