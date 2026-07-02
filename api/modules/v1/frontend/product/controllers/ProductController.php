<?php

namespace api\modules\v1\frontend\product\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseHelper;
use api\modules\v1\frontend\product\models\Product;
use api\modules\v1\frontend\product\models\search\ProductTagSearch;
use common\models\Tag;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product\models\search\ProductSearch;

class ProductController extends Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request->queryParams;
        return ResponseBuilder::responseJson(true, (new ProductSearch())->search($request));
    }

    /**
     * @author khuongdev2001
     */
    public function actionView($slug): array
    {
        $product = Product::find()->where(["slug" => $slug])->one();
        if (!$product) {
            return ResponseBuilder::responseJson(false, null, "Product not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return ResponseBuilder::responseJson(true, compact("product"));
    }

    public function actionListTags()
    {
        $request = Yii::$app->request->queryParams;
        return ResponseBuilder::responseJson(true, (new ProductTagSearch())->search($request));
    }
}
