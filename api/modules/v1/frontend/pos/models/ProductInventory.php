<?php

namespace api\modules\v1\frontend\pos\models;

use Yii;

class ProductInventory extends \common\models\ProductInventory
{
    public $total_available;
    public $total_quantity;
    public $quantityTaken;
    public $order_id;
}
