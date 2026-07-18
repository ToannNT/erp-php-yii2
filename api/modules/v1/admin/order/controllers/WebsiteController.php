<?php

namespace api\modules\v1\admin\order\controllers;

use api\modules\v1\admin\order\models\form\UpdateOrderAfterCheckoutForm;
use common\models\OrderPaymentMethod;
use common\models\User;
use Exception;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Order as OrderAlias;
use common\models\InventoryHistory;
use common\models\InventoryIssue;
use common\models\ProductInventory;
use api\modules\v1\admin\order\models\form\OrderItemForm;
use api\modules\v1\admin\order\models\OrderItem;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\order\models\form\OrderForm;
use api\modules\v1\admin\order\models\Order;
use api\modules\v1\admin\order\models\search\OrderSearch;

/**
 * WebsiteController implements the CRUD actions for Order model.
 */
class WebsiteController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => [User::ROLE_STAFF]
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR],
                ]
            ]
        ];
        return $behaviors;
    }

    protected $errors;

    /**
     * Lists all Order models.
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * Displays a single Order model.
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        return ResponseBuilder::responseJson(true, ["order" => $this->findModel($id)]);
    }

    /**
     * Creates a new Order model.
     * @return array
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $order = new OrderForm();
        $order->load(Yii::$app->request->post());
        if (!$order->validate() || !$order->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrorSummary(true)]);
        }
        return ResponseBuilder::responseJson(true, compact("order"), "Create Order successfully");
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notPacking()
            ->notCancel()
            ->notDone()
            ->notStockOut()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $order->load(Yii::$app->request->post());
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrorSummary(true)], "Can't update order");
        }
        $order->calculate();
        $order->save(false);
        return ResponseBuilder::responseJson(true, compact("order"), "Update Order successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionAddPromotion($id)
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notPacking()
            ->notCancel()
            ->notDone()
            ->notStockOut()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request->post();
            if (!$order->addPromotion($request)) {
                throw new Exception(current(current($order->getErrors())));
            }
            $order->calculate();
            $order->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("order"), "Add Promotion successfully");
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, null, $exception->getMessage(), ApiConstant::STATUS_OK);
        }
    }

    /**
     * @throws HttpException
     */
    public function actionUpdateAfterCheckout(int $id): array
    {
        $order = UpdateOrderAfterCheckoutForm::find()->where(["id" => $id])->done()->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $order->load(Yii::$app->request->post());
        if (!$order->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrors()], "Can't Update Checkout");
        }
        if (!empty($order->payment_methods)) {
            OrderPaymentMethod::deleteAll(["order_id" => $order->id]);
            foreach ($order->payment_methods as $payment_method) {
                $orderPaymentMethod = new OrderPaymentMethod([
                    "order_id" => $order->id,
                    "payment_method_id" => $payment_method["payment_method_id"],
                    "payment" => $payment_method["payment"]
                ]);
                $orderPaymentMethod->save(false);
            }
        }
        $order->save();
        return ResponseBuilder::responseJson(true, compact("order"), "Change Order after checkout successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionRemovePromotion($id)
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notPacking()
            ->notCancel()
            ->notDone()
            ->notStockOut()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request->post();
            $dataDiscount = $order->data_discount->asArray();
            if (!is_array($dataDiscount)) {
                return false;
            }
            if (!empty($request["promotion"])) {
                foreach ($order->orderItems as $item) {
                    $item->discount_price = 0;
                    $item->save(false);
                    unset($dataDiscount["promotion_data"]);
                }
            }
            if (!empty($request["discount"])) {
                unset($dataDiscount["discount_value"], $dataDiscount["discount_type"]);
            }
            $order->data_discount = $dataDiscount;
            $order->calculate();
            return ResponseBuilder::responseJson(true, compact("order"));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @throws HttpException
     */
    public function actionAddOrderItem($id): array
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notPacking()
            ->notCancel()
            ->notDone()
            ->notStockOut()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        if (empty($order->inventory_id) || empty($order->office_id)) {
            return ResponseBuilder::responseJson(false, ["Inventory or Office_id not found"]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request->post();
            $orderItem = OrderItemForm::find()->where(["order_id" => $id])
                ->andWhere(["product_variant_id" => $request["product_variant_id"] ?? 0])
                ->one()
                ?: new OrderItemForm(["order_id" => $id]);
            $orderItem->order = $order;
            $orderItem->load($request);
            if (!$orderItem->validate()) {
                $errors = $orderItem->getErrorSummary(true);
                return ResponseBuilder::responseJson(false, ["errors" => $errors], current($errors));
            }
            $orderItem->calculate();
            $orderItem->save();
            $order->calculate();
            $order->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("order"), "Add Order Item successfully");
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $exception->getMessage()]);
        }
    }

    /**
     * @throws HttpException
     */
    public
    function actionRemoveOrderItem($id, $order_item_id): array
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notPacking()
            ->notCancel()
            ->notDone()
            ->notStockOut()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        OrderItem::deleteAll(["id" => $order_item_id]);
        $order->calculate();
        $order->save();
        return ResponseBuilder::responseJson(true, compact("order"), "Remove Order Item successfully");
    }

    /**
     * @throws HttpException
     */
    public
    function actionRemoveAllOrderItem($id): array
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notPacking()
            ->notCancel()
            ->notDone()
            ->notStockOut()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        OrderItem::deleteAll(["order_id" => $id]);
        $order->calculate();
        $order->save();
        return ResponseBuilder::responseJson(true, compact("order"), "Remove Order Item successfully");
    }

    /**
     * @throws HttpException|NotFoundHttpException
     */
    public function actionChangeStatus(int $id): array
    {
        $order = $this->findModel($id);
        $status = Yii::$app->request->post("status");
        if ($status === null || !is_numeric($status)) {
            return ResponseBuilder::responseJson(false, null, "Missing or invalid 'status'", ApiConstant::STATUS_BAD_REQUEST);
        }
        $status = (int)$status;
        if (!$order->canChangeStatusTo($status)) {
            return ResponseBuilder::responseJson(
                false,
                ["allowed" => Order::MANUAL_STATUS_TRANSITIONS[$order->status] ?? []],
                "Cannot change status from {$order->status} to {$status}",
                ApiConstant::STATUS_BAD_REQUEST
            );
        }
        if ($status === OrderAlias::STATUS_CANCEL) {
            $order->return_note = Yii::$app->request->post("return_note", $order->return_note);
        }
        if ($status === OrderAlias::STATUS_DONE) {
            $order->done_at = date("Y-m-d H:i:s");
        }
        $order->status = $status;
        $order->addProgressStatus($status);
        if (!$order->save(false)) {
            return ResponseBuilder::responseJson(false, ["errors" => $order->getErrorSummary(true)], "Can't change status", ApiConstant::STATUS_BAD_REQUEST);
        }
        return ResponseBuilder::responseJson(true, compact("order"), "Change status successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionApproved(int $id): array
    {
        $order = Order::find()->where(["id" => $id])
            ->order()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $order->status = Order::STATUS_APPROVED;
        $order->addProgressStatus(Order::STATUS_APPROVED);
        if ($order->save(false)) {
            return ResponseBuilder::responseJson(true, compact("order"), "Change status Approved successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't change status Approved");
    }


    /**
     * @throws HttpException
     */
    public function actionPacking(int $id): array
    {
        $order = Order::find()->where(["id" => $id])
            ->approved()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $errors = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                $productInventory = ProductInventory::interactive($order, $item);
                if (!$productInventory->checkAvailableCurrent($item->quantity)) {
                    throw new Exception("Quantity Product Inventory invalid, only accept <= {$productInventory->available}");
                }
                $productInventory
                    ->addAvailable(-$item->quantity)
                    ->addOnWay($item->quantity)
                    ->save(false);
            }
            $order->status = Order::STATUS_PACKING;
            $order->addProgressStatus(Order::STATUS_PACKING);
            $order->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("order"), "Change status Packing successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], $e->getMessage());
        }
    }

    /**
     * @throws HttpException
     */
    public
    function actionStockout(int $id): array
    {
        $order = Order::find()->where(["id" => $id])
            ->packing()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                $productInventory = ProductInventory::interactive($order, $item)
                    ->addOnWay(-$item->quantity)
                    ->addComitted($item->quantity)
                    ->save(false);
            }
            $order->status = Order::STATUS_STOCK_OUT;
            $order->addProgressStatus(Order::STATUS_STOCK_OUT);
            $order->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("order"), "Change status Stockout successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't change status Stockout");
        }
    }

    /**
     * @throws HttpException
     */
    public function actionDone(int $id): array
    {
        $order = Order::find()->where(["id" => $id])
            ->stockout()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order->addUsedPromotion();
            $orderItems = $order->orderItems;
            foreach ($orderItems as $item) {
                $productInventory = ProductInventory::interactive($order, $item)
                    ->addComitted(-$item->quantity)
                    ->addQuantity(-$item->quantity);
                $productInventory->save(false);
                InventoryHistory::create([
                    "action" => InventoryHistory::ACTION_INVENTORY_ISSUE_FOR_ORDER,
                    "created_by" => $order->created_by,
                    "inventory" => $productInventory->available,
                    "change_quantity" => "- $item->quantity",
                    "voucher_code" => $order->code,
                    "inventory_id" => $order->inventory_id,
                    "product_id" => $item->product_id,
                    "product_variant_id" => $item->product_variant_id,
                    "type" => InventoryHistory::TYPE_ORDER
                ]);
            }
            InventoryIssue::doneOrder($order, $orderItems);
            $order->status = Order::STATUS_DONE;
            $order->done_at = date("Y-m-d H:i:s");
            $order->addProgressStatus(Order::STATUS_DONE);
            $order->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("order"), "Change status Done successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't change status Done");
        }
    }

    /**
     * @throws HttpException
     */
    public function actionCancel(int $id): array
    {
        $order = OrderForm::find()->where(["id" => $id])
            ->notCancel()
            ->one();
        if (!$order) {
            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!in_array($order->status, [OrderAlias::STATUS_ORDER, OrderAlias::STATUS_APPROVED, OrderAlias::STATUS_DONE])) {
                $this->caseCancel($order);
            }
            if ($order->status == OrderAlias::STATUS_DONE) {
                $order->status = Order::STATUS_RETURN;
                $order->calculateCancel();
                $order->return_note = Yii::$app->request->post("return_note");
                $order->addProgressStatus(OrderAlias::STATUS_RETURN);
            } else {
                $order->status = OrderAlias::STATUS_CANCEL;
                $order->addProgressStatus(OrderAlias::STATUS_CANCEL);
            }
            if (!$order->save(false)) {
                throw new Exception(current($order->getErrorSummary(true)));
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("order"), "Change status Cancel successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], $e->getMessage());
        }
    }

    /**
     * @throws HttpException
     * @version 2.0
     */
    //    public function actionReturn(int $id): array
    //    {
    //        $order = Order::find()->where(["id" => $id])->done()->one();
    //        if (!$order) {
    //            return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
    //        }
    //        $transaction = Yii::$app->db->beginTransaction();
    //        try {
    //            $request = Yii::$app->request;
    //            $order->status = OrderAlias::STATUS_RETURN;
    //            $order->return_note = $request->post("return_note");
    //            $order->calculateCancel();
    //            $order->save(false);
    //            $transaction->commit();
    //            return ResponseBuilder::responseJson(false, compact("order"), "Return Order successfully");
    //        } catch (Exception $exception) {
    //            $transaction->rollBack();
    //            return ResponseBuilder::responseJson(false, null, "Can't Return Order");
    //        }
    //    }

    protected function caseCancel(&$order)
    {
        foreach ($order->orderItems as $item) {
            $productInventory = ProductInventory::interactive($order, $item)
                ->addAvailable($item->quantity);
            switch ($order->status) {
                case Order::STATUS_PACKING:
                    $productInventory->addOnWay($item->quantity);
                    break;
                case Order::STATUS_STOCK_OUT:
                    $productInventory->addComitted($item->quantity);
                    break;
            }
            $productInventory->save(false);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Order
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
