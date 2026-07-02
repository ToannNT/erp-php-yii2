<?php

namespace common\models;

use Yii;
use \common\models\base\InventoryReceiptItem as BaseInventoryReceiptItem;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "inventory_receipt_item".
 */
class InventoryReceiptItem extends BaseInventoryReceiptItem
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
