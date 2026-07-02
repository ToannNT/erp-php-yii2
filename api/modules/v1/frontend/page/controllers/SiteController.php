<?php

namespace api\modules\v1\frontend\page\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\page\models\search\PageSearch;
use Yii;

class SiteController extends \yii\rest\Controller
{
    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionView(): array
    {
        $model = new PageSearch();
        $page = $model->searchFind(Yii::$app->request->queryParams);
        if ($page) {
            return ResponseBuilder::responseJson(true, compact("page"));
        }
        return ResponseBuilder::responseJson(false,
            null, "{module} not found",
            ApiConstant::STATUS_NOT_FOUND);
    }
}