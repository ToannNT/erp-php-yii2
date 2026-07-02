<?php

namespace common\models;

use Yii;
use \common\models\base\CmsProductFeatured as BaseCmsProductFeatured;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_product_featured".
 */
class CmsProductFeatured extends BaseCmsProductFeatured
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
