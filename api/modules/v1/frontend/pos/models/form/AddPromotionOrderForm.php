<?php

namespace api\modules\v1\frontend\pos\models\form;

use api\modules\v1\frontend\pos\models\Order;
use common\models\Promotion;
use common\validators\IsArrayValidator;

class AddPromotionOrderForm extends Order
{
    public $codes;

    public function rules()
    {
        return [
            ["codes", IsArrayValidator::className(), "skipOnEmpty" => false],
        ];
    }
}
