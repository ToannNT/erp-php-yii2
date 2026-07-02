<?php

namespace api\modules\v1\frontend\pos\models\form;

class SaveOrderItemCustomForm extends SaveOrderItemForm
{
    public function rules(): array
    {
        return [
            [["order_id", "quantity", "name"], "required"],
            [["order_id"], "checkOrderRule"],
            [["quantity", "discount_type"], "integer", "min" => 1],
            [["unit_price", "discount_value"], "number"],
            [["note", "tax_reason", "discount_reason"], "string"],
            [["discount_value"], "checkDiscountTypeRule"],
            [["tax_value"], "number", "max" => 100]
        ];
    }
}