<?php

namespace api\modules\v1\frontend\pos\controllers;

use Yii;
use api\helper\response\{
    ApiConstant,
    ResponseBuilder
};
use api\modules\v1\frontend\pos\models\search\ProductVariantSearch;
use yii\rest\Controller;

class ProductVariantController extends Controller
{
    /**
     * @throws yii\web\HttpException
     */
    public function actionIndex(): array
    {
        $model = new ProductVariantSearch();
        return ResponseBuilder::responseJson(
            true,
            $model->search(Yii::$app->request->queryParams)
        );
    }

    /**
     * @throws yii\web\HttpException
     */
    // public function actionView(): array
    // {
    //     $productVariant = (new ProductVariantSearch())->searchFind(Yii::$app->request->queryParams);
    //     if ($productVariant) {
    //         return ResponseBuilder::responseJson(true, ["product_variant" => $productVariant]);
    //     }
    //     return ResponseBuilder::responseJson(false, null, "Product not found", ApiConstant::STATUS_NOT_FOUND);
    // }
}
