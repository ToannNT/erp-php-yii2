<?php

namespace console\controllers;

use common\models\InventoryIssue;
use common\models\InventoryIssueItem;
use common\models\OrderPaymentMethod;
use common\models\ProductInventory;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\db\StaleObjectException;

class ToolFixDuplicateOrderPaymentMethodController extends Controller
{
    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionRun()
    {
        // get order payment method duplicate
        $orderPaymentMethodDuplicates = OrderPaymentMethod::find()->groupBy(["order_id", "payment"])->having("COUNT(`id`)>1")->all();
        if (count($orderPaymentMethodDuplicates) == 0) {
            echo "Can't find the order duplicate!";
            die;
        }
        if ($this->confirm("found " . count($orderPaymentMethodDuplicates) . " order duplicate: " . implode(",", array_column($orderPaymentMethodDuplicates, "order_id")) . ". Do you want to recovery orders?")) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($orderPaymentMethodDuplicates as $orderPaymentMethodDuplicate) {
                    echo "Recovering Order Payment Method OrderId: {$orderPaymentMethodDuplicate->order_id}" . PHP_EOL;
                    $this->recoveryOrderPaymentMethod($orderPaymentMethodDuplicate->order_id);
                    $orderPaymentMethodDuplicate->delete();
                    echo "Delete OrderPayment Method success OrderId: {$orderPaymentMethodDuplicate->order_id}" . PHP_EOL;
                }
            } catch (\Exception $exception) {
                $transaction->rollBack();
                echo $exception->getMessage();
            }
            echo "Khôi phục thành công chúc bạn sau này code không bị bug" . PHP_EOL;
            $transaction->commit();
        }
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function recoveryOrderPaymentMethod($order_id)
    {
        $this->recoveryInventoryIssue($order_id);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function recoveryInventoryIssue($order_id)
    {
        echo "Recovering InventoryIssue Method OrderId: {$order_id}" . PHP_EOL;
        $inventoryIssues = InventoryIssue::find()->where(["order_id" => $order_id])->all();
        if (count($inventoryIssues) > 1) {
            $this->recoveryInventoryIssueItems($inventoryIssues[0]->inventory_id, $inventoryIssues[0]->id);
            $inventoryIssues[0]->delete();
        }
        echo "Delete Inventory Issue InventoryIssueId:{$inventoryIssues[0]->inventory_id} successfully" . PHP_EOL;
    }

    protected function recoveryInventoryIssueItems($inventory_id, $inventory_issue_id)
    {
        echo "Recovering InventoryIssueItem Method InventoryId: {$inventory_id}" . PHP_EOL;
        $inventoryIssueItems = InventoryIssueItem::find()->where(["inventory_issue_id" => $inventory_issue_id])->all();
        foreach ($inventoryIssueItems as $inventoryIssueItem) {
            $this->recoveryProductInventory($inventoryIssueItem->quantity, $inventory_id, $inventoryIssueItem->product_variant_id);
            $inventoryIssueItem->delete();
            echo "Delete Inventory Issue Item InventoryIssueItemId:{$inventoryIssueItem->id} ,InventoryId:{$inventory_id}" . PHP_EOL;
        }
    }

    protected function recoveryProductInventory($quantity, $inventory_id, $product_variant_id)
    {
        echo "Recovering ProductInventory Method InventoryId: {$inventory_id}, ProductVariantId: {$product_variant_id}" . PHP_EOL;
        $productInventory = ProductInventory::find()->where(["inventory_id" => $inventory_id, "product_variant_id" => $product_variant_id])->one();
        $productInventory->available += $quantity;
        $productInventory->quantity += $quantity;
        $productInventory->save(false);
        echo "Recovery ProductInventory success InventoryId: {$inventory_id}, ProductVariantId: {$product_variant_id}" . PHP_EOL;
    }

}