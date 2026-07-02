<?php

namespace common\models;

use Yii;
use \common\models\base\Promotion as BasePromotion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "promotion".
 */
class Promotion extends BasePromotion
{
    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_PRICE = 2;
    const DISCOUNT_SAME_PRICE = 3;
    const PROMOTION_PRODUCT = "product";
    const PROMOTION_ORDER = "order";
    const PROMOTION_CATEGORY = "category";
    const PROMOTION_SUPPLIER = "supplier";
    const PROMOTION_BRAND = "brand";
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_EXPIRED = -1;
    const STATUS_DELETE = -99;

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

    public function softDelete()
    {
        $this->status = Promotion::STATUS_DELETE;
        return $this->save(false);
    }
}
