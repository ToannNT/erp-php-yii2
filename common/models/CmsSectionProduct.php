<?php

namespace common\models;

use Yii;
use \common\models\base\CmsSectionProduct as BaseCmsSectionProduct;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_section_products".
 */
class CmsSectionProduct extends BaseCmsSectionProduct
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
