<?php

namespace api\modules\v1\admin\general\controllers;

use api\helper\response\ResponseBuilder;
use Yii;
use api\modules\v1\admin\general\models\Inventory;
use api\modules\v1\admin\general\models\search\InventorySearch;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * InventoryController implements the CRUD actions for Inventory model.
 */
class InventoryController extends Controller
{

    /**
     * Lists all Inventory models.
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new InventorySearch())->search(Yii::$app->request->queryParams));
    }


    /**
     * Displays a single Inventory model.
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $inventory = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("inventory"));
    }

    /**
     * Creates a new Inventory model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return "Non object";
    }

    /**
     * Updates an existing Inventory model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate(int $id)
    {
        return "Non object";
    }

    /**
     * Delete an existing Inventory model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return "Non object";
    }


    /**
     * Finds the Inventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Inventory
    {
        if (($model = Inventory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
