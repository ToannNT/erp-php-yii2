<?php

namespace api\modules\v1\frontend\pos\models\form;

use api\modules\v1\frontend\pos\models\Order;
use common\models\Promotion;

class PromotionOrderForm extends Order
{  
    public function setRemoveDiscount()
    {
        $this->promotion_id = null;
        $this->discount = 0;
    }
}
