<?php

namespace api\modules\v1\admin\product\controllers;

use api\modules\v1\admin\product\models\CategoryBrand;
use api\modules\v1\admin\product\models\search\CategoryBrandSearch;
use api\helper\response\ResponseBuilder;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * CategoryBrandController implements the CRUD actions for CategoryBrand model.
 */
class CategoryBrandController extends Controller
{
    /**
     * Lists all CategoryBrand models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CategoryBrandSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider, "Category Brand successfully");
    }

    /**
     * Displays a single CategoryBrand model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return ResponseBuilder::responseJson(true, ["category_brand" => $this->findModel($id)]);
    }

    /**
     * Creates a new CategoryBrand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CategoryBrand();
        $model->load(Yii::$app->request->post());
        if (!$model->validate() || !$model->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $model->getErrors()], "Can't create Brand");
        }
        return ResponseBuilder::responseJson(true, ["data" => $model], "Create Category Brand successfully");
    }

    /**
     * Updates an existing CategoryBrand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post());
        if (!$model->validate() || !$model->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $model->getErrors()], "Can't update Brand");
        }
        return ResponseBuilder::responseJson(true, ["data" => $model], "Update Category Brand successfully");
    }

    /**
     * Deletes an existing CategoryBrand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return ResponseBuilder::responseJson(true, [], "Delete Category Brand successfully");
    }

    /**
     * Finds the CategoryBrand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CategoryBrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CategoryBrand::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
