<?php

namespace common\models;

use Yii;
use \common\models\base\ProductSupplier as BaseProductSupplier;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_supplier".
 */
class ProductSupplier extends BaseProductSupplier
{

    const SUPPLIER_STATUS_ACTIVE = 1;

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
