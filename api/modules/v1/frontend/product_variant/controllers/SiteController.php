<?php

namespace api\modules\v1\frontend\product_variant\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product_variant\models\ProductVariant;
use yii\rest\Controller;

class SiteController extends Controller
{
    public function actionView($search)
    {
        $productVariant = ProductVariant::find()->where(["OR", ["slug" => $search], ["id" => $search]])->one();
        if (!$productVariant) {
            return ResponseBuilder::responseJson(false, [], "", ApiConstant::STATUS_NOT_FOUND);
        }
        return ResponseBuilder::responseJson(true, ["product_variant" => $productVariant]);
    }
}