<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\DeliveryFee;
use api\modules\v1\admin\setting\models\form\DeliveryFeeForm;
use api\modules\v1\admin\setting\models\search\DeliveryFeeSearch;

class DeliveryFeeController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update'],
                        'roles' => ['administrator', 'manager'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['administrator'],
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
        $deliveryFee = new DeliveryFeeForm();

        $deliveryFee->load(Yii::$app->request->post());

        if (!$deliveryFee->validate() || !$deliveryFee->save()) {

            return ResponseBuilder::responseJson(false, ["errors" => $deliveryFee->getErrors()], "Can't create Delivery Fee");
        }
        return ResponseBuilder::responseJson(true, ["delivery_fee" => $deliveryFee], "Create Delivery Fee successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $deliveryFee = $this->findModel($id);
        $deliveryFee->load(Yii::$app->request->post());
        if (!$deliveryFee->validate() || !$deliveryFee->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $deliveryFee->getErrors()], "Can't update Delivery Fee");
        }
        return ResponseBuilder::responseJson(true, ["delivery_fee" => $deliveryFee], "Update Delivery Fee successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $deliveryFee = $this->findModel($id);
        if ($deliveryFee->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Delivery Fee successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Delivery Fee");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $deliveryFee = $this->findModel($id);
        return ResponseBuilder::responseJson(true, ["delivery_fee" => $deliveryFee]);
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new DeliveryFeeSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $deliveryFee = DeliveryFee::find()->andWhere(compact("id"))->unDelete()->one();
        if ($deliveryFee) {
            return $deliveryFee;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Delivery Fee not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
