<?php

namespace api\modules\v1\admin\product\controllers;

use api\helper\response\ResponseBuilder;
use Yii;
use api\modules\v1\admin\product\models\InventoryHistory;
use api\modules\v1\admin\product\models\search\InventoryHistorySearch;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * InventoryHistoryController implements the CRUD actions for InventoryHistory model.
 */
class InventoryHistoryController extends Controller
{

    /**
     * Lists all InventoryHistory models.
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new InventoryHistorySearch())->search(Yii::$app->request->queryParams));
    }


    /**
     * Displays a single InventoryHistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return "Tạm bỏ qua view";
    }

    /**
     * Creates a new InventoryHistory model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return "Tạm bỏ qua create";
    }

    /**
     * Updates an existing InventoryHistory model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return "Tạm bỏ qua cập nhật";
    }

    /**
     * Delete an existing InventoryHistory model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return "Tạm bỏ qua xóa";
    }

    /**
     * Delete multiple existing InventoryHistory model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        return "Tạm bỏ qua xóa nhiều";
    }

    /**
     * Finds the InventoryHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InventoryHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InventoryHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
