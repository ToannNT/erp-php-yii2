<?php

namespace common\models;

use Yii;
use \common\models\base\CmsProductFeature as BaseCmsProductFeature;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_product_feature".
 */
class CmsProductFeature extends BaseCmsProductFeature
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
