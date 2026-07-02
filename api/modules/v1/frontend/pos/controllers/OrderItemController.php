<?php

namespace api\modules\v1\frontend\pos\controllers;

use api\modules\v1\frontend\pos\models\form\SaveOrderItemCustomForm;
use common\models\Order;
use Exception;
use Throwable;
use Yii;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\OrderItem;
use api\modules\v1\frontend\pos\models\form\SaveOrderItemForm;
use yii\rest\Controller;
use api\modules\v1\frontend\pos\models\search\OrderSearch;
use common\models\ProductVariant;
use yii\web\HttpException;
use yii\web\Request;

class OrderItemController extends Controller
{
    /**
     * @return array
     * @author khuongdev2001
     */
    public function verbs(): array
    {
        return [
            "save" => ["POST", "PUT"]
        ];
    }

    /**
     * @return array
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionSave(): array
    {
        $request = Yii::$app->request;
        $productVariant = $this->findProductVariant($request);
        if (empty($request->post("product_variant_id"))) {
            $orderItem = SaveOrderItemCustomForm::find()->where([
                "id" => $request->post("id"),
                "order_id" => $request->post("order_id")
            ])->one() ?: new SaveOrderItemCustomForm();
        } else {
            $orderItem = SaveOrderItemForm::find()
                ->where(["order_id" => $request->post("order_id")])
                ->andWhere(["product_variant_id" => $productVariant->id])
                ->one() ?: new SaveOrderItemForm();
        }
        $orderItem->load($request->post(), "");
        if (!$orderItem->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $orderItem->getErrors()], current($orderItem->getErrorSummary(true)));
        }
        $order = OrderSearch::find()->where(["id" => $orderItem->order_id])->one();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderItem->calculate();
            $orderItem->save(false);
            $order->calculate();
            $order->save(false);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(
                false,
                ["errors" => $e->getMessage()],
                ApiConstant::STATUS_INTERNAL_SERVER_ERROR
            );
        }
        return ResponseBuilder::responseJson(true, $order, "Save Order successfully");
    }

    /**
     * @param Request $request
     * @return array|ProductVariant
     * @throws HttpException
     * Function find product variant by barcode or id
     */
    protected function findProductVariant(Request $request)
    {
        $query = ProductVariant::find();
        $query->andFilterWhere([
            "id" => $request->post("product_variant_id"),
            "barcode" => $request->post("barcode")
        ]);
        $productVariant = $query->one();
        if ($productVariant) {
            return $productVariant;
        }
        throw new HttpException(
            ApiConstant::STATUS_NOT_FOUND,
            "Product Variant Not Found",
            ApiConstant::STATUS_NOT_FOUND
        );
    }

    /**
     * @param int $id
     * @return array
     * @throws yii\web\HttpException|Throwable
     * @author khuongdev2001
     */
    public function actionDelete(int $id): array
    {
        $orderItem = OrderItem::find()
            ->joinWith("order")
            ->andWhere(["order_item.id" => $id])
            ->andWhere(["office_id" => array_column(Yii::$app->user->identity->offices, "id")])
            ->andWhere(["order.status" => Order::STATUS_ORDER])
            ->one();
        if (!$orderItem) {
            return ResponseBuilder::responseJson(false, null, "Order Item not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderItem->delete();
            $order = $orderItem->order;
            $order->calculate();
            $order->save(false);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return ResponseBuilder::responseJson(true, compact("order"), "Delete Item successfully");
    }
}
