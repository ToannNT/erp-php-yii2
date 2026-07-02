<?php

namespace common\models;

use Yii;
use \common\models\base\ProductMetum as BaseProductMetum;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_meta".
 */
class ProductMetum extends BaseProductMetum
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
