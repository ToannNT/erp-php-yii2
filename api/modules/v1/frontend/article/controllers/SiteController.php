<?php

namespace api\modules\v1\frontend\article\controllers;

use api\helper\response\ApiConstant;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\article\{
    models\Article,
    models\search\ArticleSearch
};
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @throws yii\web\HttpException
     */
    public function actionIndex(): array
    {
        $model = new ArticleSearch();
        return ResponseBuilder::responseJson(true, $model->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws yii\web\HttpException
     */
    public function actionView(): array
    {
        $model = (new ArticleSearch())->searchFind(Yii::$app->request->queryParams);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return ResponseBuilder::responseJson(true, ["article" => $model]);
    }
}