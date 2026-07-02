<?php

namespace common\models;

use Yii;
use \common\models\base\CmsBrandProductSection as BaseCmsBrandProductSection;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_brand_product_sections".
 */
class CmsBrandProductSection extends BaseCmsBrandProductSection
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
