<?php

namespace api\modules\v1\admin\general\controllers;

use api\helper\response\ResponseBuilder;
use Yii;
use api\modules\v1\admin\general\models\Supplier;
use api\modules\v1\admin\general\models\search\SupplierSearch;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
class SupplierController extends Controller
{

    /**
     * Lists all Supplier models.
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
    {
        return ResponseBuilder::responseJson(true, (new SupplierSearch())->search(Yii::$app->request->queryParams));
    }


    /**
     * Displays a single Supplier model.
     * @param integer $id
     * @return mixed
     */
    public function actionView(int $id)
    {
        return $this->findModel($id);
    }

    /**
     * Creates a new Supplier model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $supplier = new Supplier();
        $supplier->load(Yii::$app->request->post());
        if (!$supplier->validate() || !$supplier->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $supplier->getErrors()], "Can't Create Supplier");
        }
        return ResponseBuilder::responseJson(true, compact("supplier"), "Create Supplier successfully");
    }

    /**
     * Updates an existing Supplier model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return "Chưa làm";
    }

    /**
     * Delete an existing Supplier model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return "Chưa làm";
    }

    /**
     * Delete multiple existing Supplier model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        return "Chưa làm";
    }

    /**
     * Finds the Supplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Supplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Supplier::find()->where(["id" => $id])->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
