<?php

namespace api\modules\v1\admin\person\controllers;

use api\helper\response\ResponseBuilder;
use Yii;
use api\modules\v1\admin\person\models\CustomerNote;
use api\modules\v1\admin\person\models\search\CustomerNoteSearch;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerNoteController implements the CRUD actions for CustomerNote model.
 */
class CustomerNoteController extends Controller
{

    /**
     * Lists all CustomerNote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerNoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * Displays a single CustomerNote model.
     * @param int $id ID
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CustomerNote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $customerNote = new CustomerNote();
        $customerNote->load(Yii::$app->request->post());
        if ($customerNote->save()) {
            return ResponseBuilder::responseJson(true, ["customer_note" => $customerNote], "Create Customer Note successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $customerNote->getErrors()], "Can't Create Customer Note");
    }

    /**
     * Updates an existing CustomerNote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $customerNote = $this->findModel($id);

        if ($customerNote->load(Yii::$app->request->post()) && $customerNote->save()) {
            return ResponseBuilder::responseJson(true, ["customer_note" => $customerNote], "Update Customer Note successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't update Customer Note");
    }

    /**
     * Deletes an existing CustomerNote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return ResponseBuilder::responseJson(true, null, "Delete Customer Note successfully");
    }

    /**
     * Finds the CustomerNote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CustomerNote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerNote::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
