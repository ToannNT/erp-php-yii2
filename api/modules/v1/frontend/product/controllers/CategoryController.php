<?php

namespace api\modules\v1\frontend\product\controllers;

use Yii;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product\models\search\CategorySearch;
use api\modules\v1\frontend\product\models\Category;

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


    public function actionMenu(): array
    {
        $categories = Category::find()
            ->active()
            ->with("latestProducts")
            ->orderBy(["priority" => SORT_ASC, "id" => SORT_ASC])
            ->all();

        $data = array_map(function (Category $category) {
            return array_merge($category->toArray($category->fields()), [
                "latest_products" => $category->latestProducts,
            ]);
        }, $categories);

        return ResponseBuilder::responseJson(true, $data);
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionView(int $id): array
    {
        $category = $this->findModel($id);
        return ResponseBuilder::responseJson(false, compact("category"), ApiConstant::STATUS_NOT_FOUND);
    }

    public function findModel($id)
    {
        $model = Category::find()->andWhere(compact("id"))->active()->one();
        if (!$model) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Category not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return $model;
    }
}
