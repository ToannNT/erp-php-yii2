<?php

namespace api\modules\v1\admin\general\controllers;

use api\modules\v1\admin\general\models\DeliveryFee;
use Yii;
use yii\rest\Controller;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\general\models\search\DeliveryFeeSearch;
use yii\web\HttpException;

class DeliveryFeeController extends Controller
{

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new DeliveryFeeSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $deliveryFee = DeliveryFee::find()->where(["id" => $id])->active()->one();
        if ($deliveryFee) {
            return ResponseBuilder::responseJson(true, ["delivery_fee" => $deliveryFee]);
        }
        return ResponseBuilder::responseJson(false, null, "Delivery Fee not found");
    }

}