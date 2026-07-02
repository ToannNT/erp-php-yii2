<?php

namespace common\components\shipping;

use common\components\shipping\models\ShipperRequestBaseObject;

interface IShipper
{
    const COD_FREE = 0;

    public function init(ShipperRequestBaseObject $shipperRequestObject);

    public function setToken(string $token);

    public function getDetail();

    public function getPrintDetail();

    public function cancel();

    public function createOrder();

    public function calculateShippingFee();

    public function getBaseUrl();
}