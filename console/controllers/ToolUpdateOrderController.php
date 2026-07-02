<?php

namespace console\controllers;

use api\modules\v1\frontend\pos\models\InventoryIssueItem;
use api\modules\v1\frontend\pos\models\Order;
use common\models\HistoryLog;
use common\models\InventoryIssue;
use common\models\OrderDiscount;
use common\models\OrderItem;
use common\models\OrderPromotion;
use common\models\ProductVariant;
use Exception;
use Yii;
use yii\console\Controller;
use yii\db\Query;

class  ToolUpdateOrderController extends Controller
{
    public $order_id;

    public function options($actionID)
    {
        return ['order_id'];
    }

    /**
     * @throws Exception
     */
//    public function actionUpdateOrderItemByInventoryIssue()
//    {
//        $inventoryIssue = InventoryIssue::find()->where(["order_id" => $this->order_id])->one();
//        $order = Order::find()->where(["id" => $this->order_id])->one();
//        if (!$inventoryIssue || !$order) {
//            throw new Exception("Inventory Issue not found");
//        }
//        OrderItem::deleteAll(["order_id" => $this->order_id]);
//        foreach ($inventoryIssue->inventoryIssueItems as $inventoryIssueItem) {
//            /**
//             * @var InventoryIssueItem $inventoryIssueItem
//             */
//            $productVariant = ProductVariant::find()->where(["id" => $inventoryIssueItem->product_variant_id])->one();
//            $orderItem = (new \api\modules\v1\frontend\pos\models\OrderItem([
//                "order_id" => $this->order_id,
//                "product_id" => $productVariant->product_id,
//                "product_variant_id" => $inventoryIssueItem->product_variant_id,
//                "number_inventory" => $inventoryIssueItem->number_inventory,
//                "unit_price" => $productVariant->unit_price,
//                "quantity" => $inventoryIssueItem->quantity,
//            ]));
//            $transaction = Yii::$app->db->beginTransaction();
//            try {
//                $orderItem->calculate();
//                $orderItem->save();
//                $order->calculate();
//                $order->save(false);
//                $transaction->commit();
//            } catch (Exception $exception) {
//                $transaction->rollBack();
//            }
//        }
//    }

//    public function actionRestoreOrder124()
//    {
//        $historyLog = HistoryLog::find()->where(["id" => 4])->one();
//        $newData = json_decode($historyLog->new_data, true);
//        if ($newData["order"]["id"] !== 124) {
//            echo "Order 124 not found";
//        } else {
//            $transaction = Yii::$app->db->beginTransaction();
//            try {
//                $order = \common\models\Order::find()->where(["id" => 124])->one();
//                if ($order->id != 124) {
//                    throw new Exception("Order not found");
//                }
//                $order->load($newData["order"], "");
//                $order->save(false);
//                OrderItem::deleteAll(["order_id" => $order->id]);
//                foreach ($newData["order_items"] as $order_item) {
//                    (new OrderItem($order_item))->save(false);
//                }
//                $transaction->commit();
//                echo "Done";
//            } catch (Exception $exception) {
//                echo "Lỗi";
//                $transaction->rollBack();
//            }
//        }
//    }

//    public function actionUpdateInventory()
//    {
//        $orders = \common\models\Order::find()->channelPos()->all();
//        foreach ($orders as $order) {
//            $order->inventory_id = 1;
//            $order->save(false);
//        }
//    }

    public function actionUpdatePromotion()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orders = Order::find()->all();
            foreach ($orders as $order) {
                $data_discount = json_decode($order->data_discount, true);
                if (empty($data_discount["promotion_data"])) {
                    continue;
                }
                $this->deletePromotionOrder($order);
                if (!empty($data_discount["promotion_data"][0])) {
                    foreach ($data_discount["promotion_data"] as $promotion_datum) {
                        $orderDiscount = new OrderDiscount();
                        if (!$order->promotion_id) {
                            continue;
                        }
                        $orderDiscount->load($promotion_datum, "");
                        $orderDiscount->type_id = $promotion_datum["id"] ?? $order->promotion_id;
                        $orderDiscount->code = $order->promotion->code;
                        $orderDiscount->title = $order->promotion->title;
                        $orderDiscount->type = OrderDiscount::TYPE_PROMOTION;
                        $orderDiscount->order_id = $order->id;
                        $orderDiscount->save(false);
                    }
                } else {
                    $orderDiscount = new OrderDiscount();
                    if (!$order->promotion_id) {
                        continue;
                    }
                    $orderDiscount->load($data_discount["promotion_data"], "");
                    $orderDiscount->type_id = $data_discount["promotion_data"]["id"] ?? $order->promotion_id;
                    $orderDiscount->code = $order->promotion->code;
                    $orderDiscount->title = $order->promotion->title;
                    $orderDiscount->type = OrderDiscount::TYPE_PROMOTION;
                    $orderDiscount->order_id = $order->id;
                    $orderDiscount->save(false);
                }
                $order->save(false);
            }
            $transaction->commit();
            echo "done";
        } catch (Exception $exception) {
            $transaction->rollBack();
            echo "fail";
            echo $exception->getMessage() . "\n";
            echo $exception->getLine() . "\n";
            echo $exception->getCode() . "\n";
            echo $exception->getFile() . "\n";
        }
    }

    protected function deletePromotionOrder(Order $order)
    {
        OrderDiscount::deleteAll(["order_id" => $order->id]);
    }
}