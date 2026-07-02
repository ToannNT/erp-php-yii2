<?php

namespace common\models;

use Exception;
use Yii;
use \common\models\base\OrderReturnItem as BaseOrderReturnItem;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_return_item".
 */
class OrderReturnItem extends BaseOrderReturnItem
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public function getOrderReturn()
    {
        return $this->hasOne(OrderReturn::class, ["id" => "order_return_id"]);
    }

    public function getQuantityReturnEdByOrderId($order_id)
    {
        $orderReturn = self::find()->joinWith("orderReturn")->andWhere([
            "order_return_item.product_variant_id" => $this->product_variant_id,
            "order_return.order_id" => $order_id
        ])->select("sum(`order_return_item`.`quantity`) as `quantity`")->asArray()->one();
        return $orderReturn["quantity"] ?? 0;
    }

    public function getQuantityOrderItemByOrderId($order_id)
    {
        $order = Order::find()->where(["order.id" => $order_id])->joinWith("orderItems")
            ->select("sum(`order_item`.`quantity`) as `quantity`")
            ->asArray()
            ->one();
        return $order["quantity"] ?? 0;
    }

    /**
     * @param Order $order
     * @return bool
     * @throws Exception
     */
    public function addReturnEdOrderItem($order)
    {
        $orderItem = OrderItem::find()->where([
            "order_id" => $order->id,
            "product_variant_id" => $this->product_variant_id
        ])->one();
        $orderItem->quantity_return += $this->quantity;
        /* Set unit price return with order item */
        $this->unit_price = $orderItem->unit_price;
        $this->sub_total_price = $orderItem->sub_total;
        $this->total_price = $orderItem->total_price;
        if (!$orderItem->save(false)) {
            return false;
        }
        return true;
    }

    /**
     * @param OrderReturn $orderReturn
     * @return void
     * @throws Exception
     * @author khuongdev2001
     */

    public function returnProductInventory($orderReturn)
    {
        $productInventory = ProductInventory::find()->where([
            "product_variant_id" => $this->product_variant_id,
            "inventory_id" => $this->inventory_id
        ])->one();
        $productInventory
            ->addAvailable($this->quantity)
            ->addQuantity($this->quantity)
            ->save(false);
        // create history product inventory
        InventoryHistory::create([
            "inventory_id" => $productInventory->inventory_id,
            "inventory" => $productInventory->available,
            "change_quantity" => "+{$this->quantity}",
            "office_id" => $this->office_id,
            "action" => InventoryHistory::ACTION_CANCEL_ORDER,
            "voucher_code" => $orderReturn->code,
            "product_id" => $this->product_id,
            "product_variant_id" => $this->product_variant_id,
            "type" => InventoryHistory::TYPE_RETURN,
            "created_by" => Yii::$app->user->identity->getId()
        ]);
    }

    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::className(), ["id" => "product_variant_id"])->addSelect(["id", "name", "product_id", "sku", "unit_price", "sll_price", "images"]);
    }
}
