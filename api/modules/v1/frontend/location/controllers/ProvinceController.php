<?php

namespace api\modules\v1\frontend\location\controllers;

use api\modules\v1\frontend\location\models\search\DistrictSearch;
use api\modules\v1\frontend\location\models\search\WardSearch;
use Yii;
use yii\rest\Controller;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\location\models\search\ProvinceSearch;
use yii\web\HttpException;

class ProvinceController extends Controller
{

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new ProvinceSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionDistrict($province_code): array
    {
        return ResponseBuilder::responseJson(true, (new DistrictSearch([
            "province_code" => $province_code
        ]))->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionWard($district_code): array
    {
        return ResponseBuilder::responseJson(true, (new WardSearch([
            "district_code" => $district_code
        ]))->search(Yii::$app->request->queryParams));
    }

}