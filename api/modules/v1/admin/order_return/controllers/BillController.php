<?php

namespace api\modules\v1\admin\order_return\controllers;

use common\models\User;
use Exception;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use common\models\Order;
use common\models\OrderReturn as OrderReturnAlias;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\order_return\models\form\OrderReturnForm;
use api\modules\v1\admin\order_return\models\form\OrderReturnItemForm;
use api\modules\v1\admin\order_return\models\OrderReturn;
use api\modules\v1\admin\order_return\models\search\OrderReturnSearch;

/**
 * BillController implements the CRUD actions for OrderReturn model.
 */
class BillController extends Controller
{
    /**
     * Lists all OrderReturn models.
     *
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        $searchModel = new OrderReturnSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * Displays a single OrderReturn model.
     * @param int $id ID
     * @return array
     * @throws NotFoundHttpException|HttpException if the model cannot be found
     */
    public function actionView(int $id): array
    {
        return ResponseBuilder::responseJson(true, ["order_return" => $this->findModel($id)]);
    }

    /**
     * Creates a new OrderReturn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $order_id
     * @return array
     * @throws HttpException
     */
    public function actionCreate(int $order_id): array
    {
        $order = Order::find()->where(["id" => $order_id])->andWhere(["status" => Order::STATUS_DONE])->one();
        if (!$order) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Order not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $orderReturn = new OrderReturnForm(["order_id" => $order->id, "client_id" => $order->client_id]);
        $orderReturn->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        try {
            if (!$orderReturn->validate() || !$orderReturn->save()) {
                $errors = $orderReturn->getErrorSummary(true);
                throw new Exception("Error Order Return");
            }
            $quantity = 0;
            foreach ($orderReturn->order_return_items as $order_return_item) {
                $orderReturnItem = new OrderReturnItemForm([
                    "order_return_id" => $orderReturn->id,
                    "order" => $order
                ]);
                $orderReturnItem->load($order_return_item);
                if (!$orderReturnItem->validate() || !$orderReturnItem->save()) {
                    $errors = $orderReturnItem->getErrorSummary(true);
                    throw new Exception("Error Order Item Return");
                }
                if (!$orderReturnItem->addReturnEdOrderItem($order)) {
                    throw new Exception("Can't Add returned to Order Item");
                }
                $orderReturnItem->returnProductInventory($orderReturn);
                $quantity += $orderReturnItem->quantity;
                // set office_id, inventory_id default by order return item
                $orderReturn->office_id = $orderReturnItem->office_id;
                $orderReturn->inventory_id = $orderReturnItem->inventory_id;
            }
            $orderReturn->quantity = $quantity;
            $orderReturn->setFormatCode();
            $orderReturn->createOrderOrderReturn();
            $orderReturn->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["order_return" => $orderReturn]);
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, $exception->getMessage(), current($errors));
        }
    }

    /**
     * Finds the OrderReturn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return OrderReturn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): OrderReturn
    {
        $query = OrderReturn::find()->where(["id" => $id]);
        $userLogged = Yii::$app->user;
        if ($userLogged->can(User::ROLE_SELLER) || $userLogged->can(User::ROLE_MANAGER)) {
            $query->andWhere(["office_id" => array_column($userLogged->identity->offices, "id")]);
        }
        $orderReturn = $query->one();
        if ($orderReturn) {
            return $orderReturn;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
