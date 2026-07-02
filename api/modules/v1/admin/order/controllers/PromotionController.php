<?php

namespace api\modules\v1\admin\order\controllers;

use api\modules\v1\admin\order\models\Promotion;
use Yii;
use yii\rest\Controller;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\order\models\search\PromotionSearch;
use yii\web\HttpException;

class PromotionController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new PromotionSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $promotion = Promotion::find()->where(["id" => $id])->active()->available()->one();
        if (!$promotion) {
            return ResponseBuilder::responseJson(false, compact("promotion"));
        }
        return ResponseBuilder::responseJson(true, compact("promotion"));
    }
}
