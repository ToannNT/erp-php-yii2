<?php

namespace api\modules\v1\admin\order\controllers;

use api\modules\v1\admin\order\models\shippers\GHNShipper;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\order\models\CheckDeliveryFeeShipper;

class ShipperController extends Controller
{
    /**
     * @throws Exception
     * @throws InvalidConfigException
     * @throws HttpException
     */
    public function actionCalculateDeliveryFee()
    {
        $shipperErp = new CheckDeliveryFeeShipper();
        $shipperErp->load(Yii::$app->request->post());
        if (!$shipperErp->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $shipperErp->getErrors()]);
        }
        $shipperType = "\api\modules\\v1\admin\order\models\shippers\\$shipperErp->type";
        $result = (new $shipperType)->calculateDeliveryFee($shipperErp);
        if ($result) {
            return ResponseBuilder::responseJson(true, ["shipper" => $result]);
        }
        return ResponseBuilder::responseJson(false, null, "Can't Get Delivery Fee");
    }
}