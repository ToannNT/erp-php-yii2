<?php

namespace api\modules\v1\frontend\pos\models\form;

use api\modules\v1\frontend\pos\models\Order;
use common\models\Promotion;
use Yii;

class UpdateOrderForm extends Order
{
    public $tax_value = 0;
    public $tax_reason;

    public function rules()
    {
        return [
            [["client_id"], "integer", "min" => 1],
            [["note", "tax_reason"], "string"],
            [["tax_value"], "integer", "max" => 100]
        ];
    }
}
