<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\PaymentMethod;
use api\modules\v1\admin\setting\models\form\PaymentMethodForm;
use api\modules\v1\admin\setting\models\search\PaymentMethodSearch;

class PaymentMethodController extends Controller
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
        return ResponseBuilder::responseJson(true, (new PaymentMethodSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        return ResponseBuilder::responseJson(true, ["payment_method" => $this->findModel($id)]);
    }

    /**
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $paymentMethod = new PaymentMethodForm();
        $paymentMethod->load(Yii::$app->request->post());
        if (!$paymentMethod->validate() || !$paymentMethod->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $paymentMethod->getErrors()], "Can't create Payment Method", ApiConstant::STATUS_BAD_REQUEST);
        }
        return ResponseBuilder::responseJson(true, ["payment_method" => $paymentMethod], "Create Payment Method successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $paymentMethod = $this->findModel($id, PaymentMethodForm::find());
        $paymentMethod->load(Yii::$app->request->post());
        if (!$paymentMethod->validate() || !$paymentMethod->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $paymentMethod->getErrors()], "Can't update Payment Method", ApiConstant::STATUS_BAD_REQUEST);
        }
        return ResponseBuilder::responseJson(true, ["payment_method" => $paymentMethod], "Update Payment Method successfully");
    }

    /**
     * Xoá mềm — đơn cũ vẫn giữ được tên phương thức đã dùng.
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $paymentMethod = $this->findModel($id);
        if ($paymentMethod->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Payment Method successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Payment Method", ApiConstant::STATUS_BAD_REQUEST);
    }

    /**
     * @param int $id
     * @param \common\models\PaymentMethodQuery|null $query
     * @return PaymentMethod|PaymentMethodForm
     * @throws HttpException
     */
    public function findModel(int $id, $query = null)
    {
        $paymentMethod = ($query ?: PaymentMethod::find())->andWhere(compact("id"))->unDelete()->one();
        if ($paymentMethod) {
            return $paymentMethod;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Payment Method not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
