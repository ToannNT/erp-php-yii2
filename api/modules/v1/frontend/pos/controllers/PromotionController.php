<?php

namespace api\modules\v1\frontend\pos\controllers;

use Yii;
use yii\rest\Controller;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\search\PromotionSearch;
use yii\web\HttpException;

class PromotionController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        $model = new PromotionSearch();
        return ResponseBuilder::responseJson(true, $model->search(Yii::$app->request->queryParams));
    }
}
