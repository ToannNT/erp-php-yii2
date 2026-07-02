<?php

namespace api\modules\v1\admin\article\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\article\models\ArticleCategory;
use api\modules\v1\admin\article\models\search\ArticleCategorySearch;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for ArticleCategory model.
 */
class CategoryController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new ArticleCategorySearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): array
    {
        $category = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("category"));
    }


    /**
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $category = new ArticleCategory();
        $category->load(Yii::$app->request->post());
        if ($category->validate() && $category->save()) {
            return ResponseBuilder::responseJson(true, compact("category"), "Create Category successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Create Category fail");
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $category = $this->findModel($id);
        $category->load(Yii::$app->request->post());
        if ($category->validate() && $category->save()) {
            return ResponseBuilder::responseJson(true, compact("category"), "Update Category successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Update Category fail");
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id)
    {
        $category = $this->findModel($id);
        if($category->softDelete()){
            return ResponseBuilder::responseJson(true,null, "Deleted Category successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Deleted Category fail");
    }

    /**
     * Finds the ArticleCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ArticleCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArticleCategory::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
