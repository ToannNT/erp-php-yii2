<?php

namespace common\components\shipping\models;

use api\modules\v1\admin\order\models\CheckDeliveryFeeShipper;

abstract class ShipperRequestBaseObject
{

    public $checkDeliveryFeeShipper;

    public function init($httpClient)
    {

    }

    abstract public function loadServiceExtras();

    // Khai bao Luu tru Base
}