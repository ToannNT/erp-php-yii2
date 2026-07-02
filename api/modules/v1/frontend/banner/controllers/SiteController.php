<?php
namespace api\modules\v1\frontend\banner\controllers;

use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\banner\models\BannerSearch;
use yii\web\HttpException;


class SiteController extends Controller {
    /**
     * @throws HttpException
     */
    public function actionList(): array
    {
        $request = Yii::$app->request;
        $searchModel = new BannerSearch();
        if ($request->isGet) {
            $dataProvider = $searchModel->seacrh(Yii::$app->request->queryParams);
            return ResponseBuilder::responseJson(true, $dataProvider);
        }
        return ResponseBuilder::responseJson(false, null, Yii::t('api','Cannot load banner'));
    }
}