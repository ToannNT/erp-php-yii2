<?php

namespace common\models;

use Yii;
use \common\models\base\CmsProductPromotion as BaseCmsProductPromotion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_product_promotion".
 */
class CmsProductPromotion extends BaseCmsProductPromotion
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
