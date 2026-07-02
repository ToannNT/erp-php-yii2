<?php

namespace api\modules\v1\admin\order_return\models;

use common\models\OrderReturnItem as BaseOrderReturnItem;

class OrderReturnItem extends BaseOrderReturnItem
{
    public function fields()
    {
        return [
            "id",
            "order_return_id",
            "quantity",
            "unit_price",
            "sub_total_price",
            "total_price",
            "discount",
            "product_variant" => "productVariant"
        ];
    }

    public function formName()
    {
        return "";
    }
}