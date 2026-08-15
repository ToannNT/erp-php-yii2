<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\DeliveryMethod;
use api\modules\v1\admin\setting\models\form\DeliveryMethodForm;
use api\modules\v1\admin\setting\models\search\DeliveryMethodSearch;

class DeliveryMethodController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['administrator', 'manager', 'staff'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['administrator', 'manager'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new DeliveryMethodSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        return ResponseBuilder::responseJson(true, ["delivery_method" => $this->findModel($id)]);
    }

    /**
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $deliveryMethod = new DeliveryMethodForm();
        $deliveryMethod->load(Yii::$app->request->post());
        if (!$deliveryMethod->validate() || !$deliveryMethod->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $deliveryMethod->getErrors()], "Can't create Delivery Method", ApiConstant::STATUS_BAD_REQUEST);
        }
        return ResponseBuilder::responseJson(true, ["delivery_method" => $deliveryMethod], "Create Delivery Method successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $deliveryMethod = $this->findModel($id, DeliveryMethodForm::find());
        $deliveryMethod->load(Yii::$app->request->post());
        if (!$deliveryMethod->validate() || !$deliveryMethod->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $deliveryMethod->getErrors()], "Can't update Delivery Method", ApiConstant::STATUS_BAD_REQUEST);
        }
        return ResponseBuilder::responseJson(true, ["delivery_method" => $deliveryMethod], "Update Delivery Method successfully");
    }

    /**
     * Xoá mềm — đơn cũ vẫn giữ được tên phương thức đã dùng.
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $deliveryMethod = $this->findModel($id);
        if ($deliveryMethod->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Delivery Method successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Delivery Method", ApiConstant::STATUS_BAD_REQUEST);
    }

    /**
     * @param int $id
     * @param \common\models\DeliveryMethodQuery|null $query
     * @return DeliveryMethod|DeliveryMethodForm
     * @throws HttpException
     */
    public function findModel(int $id, $query = null)
    {
        $deliveryMethod = ($query ?: DeliveryMethod::find())->andWhere(compact("id"))->unDelete()->one();
        if ($deliveryMethod) {
            return $deliveryMethod;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Delivery Method not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
