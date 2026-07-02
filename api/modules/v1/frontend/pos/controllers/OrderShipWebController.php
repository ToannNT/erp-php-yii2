<?php

namespace api\modules\v1\frontend\pos\controllers;

use api\helper\response\ApiConstant;
use common\models\Order;
use Yii;
use api\modules\v1\frontend\pos\models\form\OrderShipForm;
use common\components\shipping\Client;
use common\components\shipping\shipper\GHNShipper;
use common\components\shipping\shipper\GHTKShipper;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\HttpException;

class OrderShipWebController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\QueryParamAuth::class,
            'only' => ['print-bill']
        ];
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'only' => ["print-bill"],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['manager', 'administrator', 'seller'],
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * @throws HttpException|Exception
     */
    public function actionPrintBill($order_id)
    {
        $order = $this->findModel($order_id);
        $orderShip = OrderShipForm::find()->where(["order_id" => $order->id])->one();
        /** @var Client $shipperService */
        $shipperService = Yii::$app->shipper;
        $shipperService->createShipperClient($orderShip);
        switch ($orderShip->shipper_type) {
            case GHNShipper::TYPE:
                $this->layout = false;
                return $this->renderContent($shipperService->printBill());
            case GHTKShipper::TYPE:
                header('Content-Type: application/pdf');
                echo $shipperService->printBill();
        }
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $order_id)
    {
        $order = Order::find()->where(["id" => $order_id])->one();
        if (!$order) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Order not found");
        }
        return $order;
    }
}
