<?php

namespace console\controllers;

use common\components\shipping\models\GHNRequestObject;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\httpclient\Exception;
use common\components\shipping\shipper\GHNShipper;
use yii\web\HttpException;

class InitController extends Controller
{

    /**
     * @throws InvalidConfigException
     * @throws HttpException
     */
    public function actionRun()
    {
        $shipper = new GHNShipper();
        $requestObject = new GHNRequestObject();
        $shipper->init($requestObject);
        echo "Province Running...\n";
        $shipper->synchronizedProvince();
        echo "Synchronized Province\n";
        echo "District Running...\n";
        $shipper->synchronizedDistrict();
        echo "Synchronized District\n";
        echo "Ward Running...\n";
        $shipper->synchronizedWard();
        echo "Synchronized Ward\n";
    }

}