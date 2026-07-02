<?php

namespace common\models;

use Yii;
use \common\models\base\InventoryIssueItem as BaseInventoryIssueItem;
use yii\helpers\ArrayHelper;

/**
 * Class InventoryIssueItem
 * @property ProductVariant $productVariant
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class InventoryIssueItem extends BaseInventoryIssueItem
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
        return $this->hasOne(ProductVariant::class, ['id'=>'product_variant_id']);
    }
}
