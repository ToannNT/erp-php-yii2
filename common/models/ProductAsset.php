<?php

namespace common\models;

use Yii;
use \common\models\base\ProductAsset as BaseProductAsset;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_asset".
 */
class ProductAsset extends BaseProductAsset
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
