<?php

namespace api\modules\v1\frontend\product\controllers;

use api\helper\response\ApiConstant;
use api\modules\v1\frontend\product\models\search\ProductSearch;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product\models\search\ProductInventorySearch;
use api\modules\v1\frontend\product\models\search\ProductVariantSearch;

class SiteController extends Controller
{
    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        $products = new ProductSearch();
        $dataProvider = $products->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionView(): array
    {
        $model = new ProductVariantSearch();
        $productVariant = $model->searchFind(Yii::$app->request->queryParams);
        if ($productVariant) {
            return ResponseBuilder::responseJson(true, $productVariant);
        }
        return ResponseBuilder::responseJson(false, null, Yii::t("api", "{module} Not found"), ApiConstant::STATUS_NOT_FOUND);
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionOffice(): array
    {
        $model = new ProductInventorySearch();
        return ResponseBuilder::responseJson(true, $model->search(Yii::$app->request->queryParams));
    }
}
