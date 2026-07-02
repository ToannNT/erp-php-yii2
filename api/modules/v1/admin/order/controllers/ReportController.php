<?php

namespace api\modules\v1\admin\order\controllers;

use api\helper\response\ResponseBuilder;
use Yii;
use yii\rest\Controller;
use api\modules\v1\admin\order\models\search\OrderSearch;
use yii\rest\Serializer;
use yii\web\HttpException;

class ReportController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        $dataProvider = (new OrderSearch())->search(Yii::$app->request->queryParams);
        $analytics = [
            "quantity" => 0,
            "tax_price" => 0,
            "total_price" => 0,
            "delivery_fee" => 0,
            "payments" => 0,
        ];
        foreach ($dataProvider->getModels() as $order) {
            $analytics["quantity"] += $order->quantity;
            $analytics["tax_price"] += $order->tax_price;
            $analytics["total_price"] += $order->total_price;
            $analytics["delivery_fee"] += $order->delivery_fee;
            $analytics["payments"] += $order->payments;
        }
        $serializer = new Serializer(['collectionEnvelope' => 'items']);
        $data = $serializer->serialize($dataProvider);
        $data["analytics"] = $analytics;
        return ResponseBuilder::responseJson(true, $data);
    }
}