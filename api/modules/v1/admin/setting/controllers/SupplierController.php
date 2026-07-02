<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\form\SupplierForm;
use api\modules\v1\admin\setting\models\search\SupplierSearch;

class SupplierController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administrator', 'manager'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $supplier = new SupplierForm();
        $supplier->load(Yii::$app->request->post());
        if (!$supplier->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $supplier->getErrors()], "Can't create Supplier");
        }
        $supplier->setGroup();
        if (!$supplier->save()) {
            return ResponseBuilder::responseJson(false, null, "Can't create Supplier");
        }
        return ResponseBuilder::responseJson(true, compact("supplier"), "Create Supplier successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $supplier = $this->findModel($id);
        $supplier->load(Yii::$app->request->post());
        if (!$supplier->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $supplier->getErrors()], "Can't update Supplier");
        }
        $supplier->setGroup();
        if (!$supplier->save()) {
            return ResponseBuilder::responseJson(false, null, "Can't update Supplier");
        }
        return ResponseBuilder::responseJson(true, compact("supplier"), "Update Supplier successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $supplier = $this->findModel($id);
        if ($supplier->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Supplier successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Supplier");
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new SupplierSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $supplier = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("supplier"));
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $supplier = SupplierForm::find()->where(["id" => $id])->unDelete()->one();
        if (!$supplier) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Supplier not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return $supplier;
    }
}
