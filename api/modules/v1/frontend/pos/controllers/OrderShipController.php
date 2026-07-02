<?php

namespace api\modules\v1\frontend\pos\controllers;

use api\modules\v1\frontend\pos\models\Order;
use api\modules\v1\frontend\pos\models\OrderShip;
use common\components\log\BuildLogDbTarget;
use common\components\log\DbTarget;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\modules\v1\frontend\pos\models\form\CheckPriceOrderShip;
use api\modules\v1\frontend\pos\models\form\CreateOrderShipping;
use api\modules\v1\frontend\pos\models\form\OrderShipForm;
use common\components\shipping\Client;
use common\components\shipping\shipper\GHTKShipper;
use common\components\shipping\shipper\GHNShipper;
use api\helper\response\ResponseBuilder;

class OrderShipController extends Controller
{

    /**
     * @throws HttpException
     */
    public function actionShipper($order_id): array
    {
        $order = Order::find()->where(["id" => $order_id])->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found");
        }
        $checkPriceModel = CheckPriceOrderShip::find()->where([
            "order_id" => $order_id
        ])->one();
        $checkPriceModel = $checkPriceModel ?: new CheckPriceOrderShip();
        $checkPriceModel->order = $order;
        $checkPriceModel->load(Yii::$app->request->post());
        if (!$checkPriceModel->validate() || !$checkPriceModel->save()) {
            return ResponseBuilder::responseJson(false, $checkPriceModel->getErrorSummary(true));
        }
        $shippers = [];
        $shipperService = Yii::$app->shipper;
        if ($checkPriceModel->shipper_type) {
            $shipperService->createShipperClient($checkPriceModel);
            $shippers = $shipperService->calculatorFee();
            if ($shippers["status"]) {
                // add delivery fee from shipper to order
                $order->data_other_fee = [
                    [
                        "name" => "insurance_fee",
                        "value" => $shippers["data"]["insurance_fee"]
                    ],
                ];
                $order->data_delivery_fee = [
                    "delivery_fee_value" => $shippers["data"]["ship_fee"]
                ];
                $order->calculate();
            }
        } else {
            $shipperTypes = [GHTKShipper::TYPE, GHNShipper::TYPE];
            foreach ($shipperTypes as $shipperType) {
                $checkPriceModel->shipper_type = $shipperType;
                $shipperService->createShipperClient($checkPriceModel);
                $shippers[] = $shipperService->calculatorFee();
            }
        }
        $order->type = Order::TYPE_ORDER_SHIPPER;
        $order->save(false);
        return ResponseBuilder::responseJson(true, ["shippers" => $shippers, "order" => $order]);
    }

    public function actionWebhookUpdateStatus()
    {
    }

    /**
     * @throws HttpException|Exception
     */
    public function actionPrintBill($order_id)
    {
        $orderShip = $this->findModel($order_id);
        /** @var Client $shipperService */
        $shipperService = Yii::$app->shipper;
        $shipperService->createShipperClient($orderShip);
        switch ($orderShip->shipper_type) {
            case GHNShipper::TYPE:
                $this->layout = false;
                return $this->renderContent($shipperService->printBill());
            case GHTKShipper::TYPE:
                Yii::$app->response->headers->set("content-type", "application/pdf");
                return Yii::$app->response->sendContentAsFile($shipperService->printBill(), "tesster", [
                    ["content-type" => "application/pdf"]
                ]);
        }
    }

    /**
     * @throws HttpException|\yii\db\Exception
     * @throws Throwable
     */
    public function actionCreateOrder($order_id): array
    {
        $order = CreateOrderShipping::find()
            ->pending()
            ->andWhere(["id" => $order_id])
            ->andWhere(["office_id" => array_column(Yii::$app->user->identity->offices, "id")])
            ->notWattingShipper()
            ->channelPos()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found");
        }
        $order->load(Yii::$app->request->post());
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $order->getErrors()
            ], current($order->getErrorSummary(true)));
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order->savePaid();
//            $order->savePaymentMethods($order->payment_methods);
            $orderShip = OrderShipForm::find()->where(["order_id" => $order_id])->one() ?: new OrderShipForm();
            $orderShip->load(Yii::$app->request->post());
            $orderShip->order = $order;
            if (!$orderShip->validate()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $orderShip->getErrorSummary(true)], current($orderShip->getErrorSummary(true)));
            }
            /** @var Client $shipperService */
            $shipperService = Yii::$app->shipper;
            if (!Yii::$app->request->post("insure_is")) {
                $orderShip->value = OrderShip::UN_SET_INSURE;
            }
            $shipperService->createShipperClient($orderShip);
            $shippers = $shipperService->createOrder();
            if (!$shippers || !$shippers["result"]) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $shippers["errors"]], current($shippers["errors"]));
            }
            if (!$orderShip->createOrder($shippers)) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $orderShip->getErrorSummary(true)], current($orderShip->getErrorSummary(true)));
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $e->getMessage()], $e->getMessage());
        }
        (new BuildLogDbTarget())->push("Create Order Ship", __METHOD__, DbTarget::TAG_CREATED, [
            "order" => $order->getAttributes(),
            "order_items" => $order->getOrderItems()->asArray()->all(),
            "order_ship" => $orderShip->getAttributes(),
            "data_promotion" => $order->getPromotions()->asArray()->all()
        ]);
        $transaction->commit();
        return ResponseBuilder::responseJson(true, compact("order"), "Create Order Shipper successfully");
    }

    /**
     * @throws HttpException|Exception|Throwable
     */
    public function actionCancelBillLading($order_id): array
    {
        $orderShip = OrderShipForm::find()->where(["order_id" => $order_id])->notCancelBillLading()->one();
        if (!$orderShip) {
            return ResponseBuilder::responseJson(false, null, "Order Ship not found");
        }
        /** @var Client $shipperService * */
        $shipperService = Yii::$app->shipper;
        $shipperService->createShipperClient($orderShip);
        if ($shipperService->cancel() && $orderShip->cancelBillLading()) {
            (new BuildLogDbTarget())->push("Cancel BillLading Order Ship", __METHOD__, DbTarget::TAG_CREATED, $orderShip->getAttributes(), $orderShip->getOldAttributes());
            return ResponseBuilder::responseJson(true, null, "Cancel Bill Lading successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Cancel Bill Lading fail");
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $order_id)
    {
        $orderShip = OrderShipForm::find()->where(["order_id" => $order_id])->one();
        if (!$orderShip) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Order Ship not found");
        }
        return $orderShip;
    }
}
