<?php

namespace api\modules\v1\admin\order_return\models\form;

use common\models\Order;
use common\models\OrderReturn as OrderReturnAlias;
use api\modules\v1\admin\order_return\models\OrderReturn;
use common\validators\DiscountValidator;
use common\validators\IsArrayValidator;
use Yii;

class OrderReturnForm extends OrderReturn
{
    public $order_return_items;

    public function rules()
    {
        return [
            [["sub_total_price", "total_price"], "required"],
            ["discount_type", "in", "range" => [OrderReturnAlias::DISCOUNT_PRICE, OrderReturnAlias::DISCOUNT_PERCENT]],
            ["discount_value", DiscountValidator::class, "discountType" => "discount_type"],
            [["discount", "delivery_fee", "sub_total_price", "total_price", "payments"], "number", "min" => 0],
            ["note", "string"],
            ["order_return_items", IsArrayValidator::class, "skipOnEmpty" => false, "min" => 1]
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->status = OrderReturnAlias::STATUS_ACTIVE;
            $this->created_by = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }

}