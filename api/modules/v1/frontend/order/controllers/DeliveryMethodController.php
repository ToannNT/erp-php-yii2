<?php

namespace api\modules\v1\frontend\order\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\order\models\search\DeliveryMethodSearch;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;

/**
 * Phương thức giao hàng cho website checkout — chỉ đọc, không cần auth.
 */
class DeliveryMethodController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new DeliveryMethodSearch())->search(Yii::$app->request->queryParams));
    }
}
