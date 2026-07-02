<?php

namespace api\modules\v1\admin\setting\controllers;

use common\models\Shipper as ShipperAlias;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\Shipper;
use api\modules\v1\admin\setting\models\search\ShipperSearch;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * ShipperController implements the CRUD actions for Shipper model.
 */
class ShipperController extends Controller
{
    /**
     * Lists all Shipper models.
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        $searchModel = new ShipperSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(false, $dataProvider);
    }

    /**
     * Displays a single Shipper model.
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $shipper = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("shipper"));
    }

    /**
     * Creates a new Shipper model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return array
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $shipper = new Shipper();
        $shipper->load(Yii::$app->request->post());
        if (!$shipper->validate() || !$shipper->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $shipper->getErrorSummary(true)]);
        }
        return ResponseBuilder::responseJson(true, compact("shipper"), "Create Shipper successfully");
    }

    /**
     * Updates an existing Shipper model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return array
     * @throws NotFoundHttpException|HttpException
     */
    public function actionUpdate(int $id): array
    {
        $shipper = $this->findModel($id);
        $shipper->load(Yii::$app->request->post());
        if (!$shipper->validate() || !$shipper->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $shipper->getErrorSummary(true)]);
        }
        return ResponseBuilder::responseJson(true, compact("shipper"), "Update Shipper successfully");
    }

    /**
     * Delete an existing Shipper model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function actionDelete(int $id)
    {
        $shipper = $this->findModel($id);
        $shipper->status = ShipperAlias::STATUS_DELETE;
        $shipper->save(false);
        return ResponseBuilder::responseJson(false, null, "Deleted Shipper");
    }


    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the Shipper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shipper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shipper::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
