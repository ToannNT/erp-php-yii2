<?php

namespace common\models;

use Yii;
use \common\models\base\ProductCategory as BaseProductCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_category".
 */
class ProductCategory extends BaseProductCategory
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
