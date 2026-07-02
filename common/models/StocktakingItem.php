<?php

namespace common\models;

use Yii;
use \common\models\base\StocktakingItem as BaseStocktakingItem;
use yii\helpers\ArrayHelper;

/**
 * Class StocktakingItem
 * @property ProductVariant $productVariant
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class StocktakingItem extends BaseStocktakingItem
{
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ['id' => 'product_variant_id']);
    }
}
