<?php

namespace api\modules\v1\admin\person\controllers;

use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\person\models\ContactCustomer;
use api\modules\v1\admin\person\models\search\ContactCustomerSearch;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

/**
 * ContactCustomerController implements the CRUD actions for ContactCustomer model.
 */
class ContactCustomerController extends Controller
{

    /**
     * Lists all ContactCustomer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactCustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * Displays a single ContactCustomer model.
     * @param int $id ID
     * @return mixed
     */
    public function actionView($id)
    {
        return ResponseBuilder::responseJson(true, ["contact_customer", $this->findModel($id)]);
    }

    /**
     * Creates a new ContactCustomer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $contactCustomer = new ContactCustomer();

        if ($contactCustomer->load(Yii::$app->request->post()) && $contactCustomer->save()) {
            return ResponseBuilder::responseJson(true, ["contact_customer" => $contactCustomer], "Create Contact Customer successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $contactCustomer->getErrors()], "Can't create Contact Customer");
    }

    /**
     * Updates an existing ContactCustomer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $contactCustomer = $this->findModel($id);

        if ($contactCustomer->load(Yii::$app->request->post()) && $contactCustomer->save()) {
            return ResponseBuilder::responseJson(true, ["contact_customer" => $contactCustomer], "Update Contact Customer successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $contactCustomer->getErrors()], "Can't update Contact Customer");
    }

    /**
     * Deletes an existing ContactCustomer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->softDelete();
        return ResponseBuilder::responseJson(true, null, "Deleted Contact Customer successfully");
    }

    /**
     * Finds the ContactCustomer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ContactCustomer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContactCustomer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
