<?php

namespace api\modules\v1\frontend\article\controllers;

use Yii;
use api\helper\response\ApiConstant;
use api\modules\v1\frontend\article\models\Category;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\article\models\search\CategorySearch;

class CategoryController extends Controller
{
    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        $model = new CategorySearch();
        return ResponseBuilder::responseJson(
            true,
            $model->search(Yii::$app->request->queryParams)
        );
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionView($id): array
    {
        $article = Category::find()->where(["id" => $id])->active()->one();
        if ($article) {
            return ResponseBuilder::responseJson(true, compact("article"));
        }
        return ResponseBuilder::responseJson(false,
            null,
            Yii::t("api", "{module} not found")
            , ApiConstant::STATUS_NOT_FOUND);
    }
}