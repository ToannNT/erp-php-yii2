<?php

namespace api\modules\v1\frontend\order\models\form;

use api\modules\v1\admin\inventory\models\ProductVariant;
use Aws\RAM\Exception\RAMException;
use common\models\OrderItem;
use Yii;

class OrderItemForm extends OrderItem
{
    const DEFAULT_DISCOUNT_PRICE = 0;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['product_id', 'product_variant_id', 'quantity', 'unit_price'], 'required'],
            ['unit_price', 'checkUnitPrice'],

        ]);
    }


    public function checkUnitPrice($attribute)
    {
        $productVariant = ProductVariant::find()->select("unit_price")->where(["id" => $this->product_variant_id])->one();
        if (!$productVariant) {
            $this->addError($attribute, "This product variant is not available.");
            return false;
        }
        if ($productVariant->unit_price != $this->unit_price) {
            $this->addError($attribute, "Unit price {$this->unit_price} is invalid");
            return false;
        }
        return true;
    }
}
