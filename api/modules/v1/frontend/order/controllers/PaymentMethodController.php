<?php

namespace api\modules\v1\frontend\order\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\order\models\search\PaymentMethodSearch;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;

/**
 * Phương thức thanh toán cho website checkout — chỉ đọc, không cần auth.
 */
class PaymentMethodController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new PaymentMethodSearch())->search(Yii::$app->request->queryParams));
    }
}
