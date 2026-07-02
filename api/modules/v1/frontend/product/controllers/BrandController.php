<?php

namespace api\modules\v1\frontend\product\controllers;

use Yii;
use api\modules\v1\frontend\product\models\search\BrandSearch;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product\models\search\BrandCategorySearch;

class BrandController extends Controller
{
    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionCategory(): array
    {
        $brands = (new BrandCategorySearch())->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $brands);
    }
    /**
     * @return array
     * @throws yii\web\HttpException
     */
    public function actionIndex(): array
    {
        $brands = (new BrandSearch())->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $brands);
    }

    /**
     * @throws yii\web\HttpException
     */
    public function actionView(): array
    {
        $brands = (new BrandSearch())->searchFind(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $brands);
    }

}