<?php

namespace api\modules\v1\frontend\pos\controllers;

use common\helpers\ArrayHelper;
use common\models\OrderDiscount;
use common\models\OrderPromotion;
use SamIT\Yii2\Components\Map;
use Throwable;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use common\models\Order as OrderAlias;
use api\modules\v1\frontend\pos\models\Order;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\form\CreateOrderForm;
use api\modules\v1\frontend\pos\models\form\CheckoutOrderForm;
use api\modules\v1\frontend\pos\models\form\PromotionOrderForm;
use api\modules\v1\frontend\pos\models\form\AddPromotionOrderForm;
use api\modules\v1\frontend\pos\models\search\OrderSearch;
use api\modules\v1\frontend\pos\models\form\UpdateOrderForm;
use api\modules\v1\frontend\pos\models\search\HistoryOrderSearch;
use common\components\log\BuildLogDbTarget;
use common\components\log\DbTarget;
use Exception;

class OrderController extends Controller
{

    public $order;

    public function verbs()
    {
        return [
            'cancel' => ['POST'],
            'calculate' => ['POST'],
            'add-promotion' => ['POST'],
            'remove-promotion' => ['POST'],
            'checkout' => ['POST'],
            'history' => ['GET']
        ];
    }


    /**
     * @return array
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionCreate(): array
    {
        $order = new CreateOrderForm();
        $order->load(Yii::$app->request->post(), "");
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrors()]);
        }
        $order->addProgressStatus(OrderAlias::STATUS_ORDER);
        $order->save(false);
        return ResponseBuilder::responseJson(true, compact("order"), "Create Order successfully");
    }

    /**
     * @param int $id
     * @return array
     * @throws yii\web\HttpException
     * @throws \yii\base\Exception|Throwable
     * @author khuongdev2001
     */

    public function actionCancel(int $id): array
    {
        $order = $this->findModel($id, Order::find(), false);
        $order->status = OrderAlias::STATUS_CANCEL;
        /** @var Client $shipperService */
        if ($order->type == OrderAlias::TYPE_ORDER_SHIPPER && $order->status == OrderAlias::STATUS_WATING_SHIPPER) {
            $orderShip = OrderShipForm::find()->where(["order_id" => $order->id])->one();
            if ($orderShip) {
                $shipperService = Yii::$app->shipper;
                $shipperService->createShipperClient($orderShip);
                $shipperService->cancel();
            }
        }
        (new BuildLogDbTarget())->push("Cancel Order", __METHOD__, DbTarget::TAG_DELETED, [
            "order" => $order->getAttributes(),
            "order_items" => $order->getOrderItems()->asArray()->all()
        ]);
        $order->save(false);
        return ResponseBuilder::responseJson(true, null, "Cancel Order successfully");
    }

    /**
     * @return array
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        $dataProvider = (new OrderSearch())->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     * @author khuongdev2001
     * Here is action calculate and update order in database
     */
    public function actionCalculate($id): array
    {
        $request = Yii::$app->request;
        $order = $this->findModel($id, UpdateOrderForm::find());
        $order->load($request->post(), "");
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, ["error" => $order->getErrors()]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order->calculate();
            $order->save();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => "Calculate fail"], "Calculate fail");
        }
        return ResponseBuilder::responseJson(true, compact("order"), "Update Order successfully");
    }

    /**
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public function actionAddPromotion(int $order_id)
    {
        $order = $this->findModel($order_id, AddPromotionOrderForm::find());
        $order->load(Yii::$app->request->post());
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrors()], current($order->getErrorSummary(true)));
        }
        OrderDiscount::deleteAll(["order_id" => $order->id]);
        $order->addMultiplePromotion(array_unique($order->codes));
        if ($order->hasErrors()) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrors()], current($order->getErrorSummary(true)));
        }
        $order->calculate();
        $order->save(false);
        return ResponseBuilder::responseJson(true, compact("order"), "Add Promotion successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionRemovePromotion(int $order_id)
    {
        $order = $this->findModel($order_id, PromotionOrderForm::find());
        // delete all promotion order
        OrderDiscount::deleteAll(["order_id" => $order_id]);
        $order->calculate();
        $order->save(false);
        return ResponseBuilder::responseJson(true, compact("order"), "Remove Promotion successfully");
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     * @throws Yii\db\Exception|Throwable
     * @author khuongdev2001
     */
    public function actionCheckout($id): array
    {
        /** @var CheckoutOrderForm $order */
        $order = $this->findModel($id, CheckoutOrderForm::find());
        $order->load(Yii::$app->request->post());
        $this->order = $order;
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $order->getErrors()
            ], current($order->getErrorSummary(true)));
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            $order->updateInventory();
            $order->addUsedPromotion();
            $order->savePaid();
            $order->savePaymentMethods($order->payment_methods);
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, $e->getMessage(), "Some product unconditional quantity please check order or reload page");
        }
        $transaction->commit();
        return ResponseBuilder::responseJson(true, compact("order"), "Checkout Order successfully");
    }

    /**
     * @param integer $id
     * @param CheckoutOrderForm|UpdateOrderForm|Order $model
     * @param bool $onlyPending
     * @return CheckoutOrderForm|UpdateOrderForm|Order
     * @throws HttpException
     * @author khuongdev2001
     */
    protected function findModel(int $id, $model, $onlyPending = true)
    {
        if ($onlyPending) {
            $model->pending();
        }
        $order = $model->andWhere(["id" => $id])
            ->andWhere(["office_id" => array_column(Yii::$app->user->identity->offices, "id")])
            ->channelPos()
            ->one();
        if (!$order) {
            throw new HttpException(
                ApiConstant::STATUS_NOT_FOUND,
                "Order Not Found",
                ApiConstant::STATUS_NOT_FOUND
            );
        }
        return $order;
    }

    /**
     * @throws HttpException
     */
    public function actionView($id): array
    {
        $order = $this->findModel($id, (new Order())->find(), false);
        if ($order->type == OrderAlias::TYPE_ORDER_SHIPPER && $order->status == Order::STATUS_ORDER) {
            $order->clearDelivery();
            $order->calculate();
            $order->save(false);
        }
        return ResponseBuilder::responseJson(true, compact("order"));
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionHistory(): array
    {
        $dataProvider = (new HistoryOrderSearch())->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }
}
