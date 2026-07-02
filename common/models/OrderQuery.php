<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderQuery extends \common\models\base\ActiveQuery
{
    public function active(): OrderQuery
    {
        return $this->andWhere(["status" => Order::STATUS_ACTIVE]);
    }

    public function withoutCancel(): OrderQuery
    {
        return $this->andWhere(["not in", "status", [
            Order::STATUS_CANCEL,
        ]]);
    }

    public function approved(): OrderQuery
    {
        return $this->andWhere(["status" => Order::STATUS_APPROVED]);
    }

    public function notApproved(): OrderQuery
    {
        return $this->andWhere(["<>", "order.status", Order::STATUS_CANCEL]);
    }

    public function notOrder(): OrderQuery
    {
        return $this->andWhere(["<>", "order.status", Order::STATUS_ORDER]);
    }

    public function stockout(): OrderQuery
    {
        return $this->andWhere(["status" => Order::STATUS_STOCK_OUT]);
    }

    public function packing(): OrderQuery
    {
        return $this->andWhere(["status" => Order::STATUS_PACKING]);
    }

    public function order(): OrderQuery
    {
        return $this->andWhere(["order.status" => Order::STATUS_ORDER]);
    }

    public function done(): OrderQuery
    {
        return $this->andWhere(["status" => [Order::STATUS_DONE, Order::STATUS_WATING_SHIPPER]]);
    }

    public function notCancel(): OrderQuery
    {
        return $this->andWhere(["<>", "status", Order::STATUS_CANCEL]);
    }

    public function notDone(): OrderQuery
    {
        return $this->andWhere(["<>", "status", Order::STATUS_DONE]);
    }

    public function notPacking(): OrderQuery
    {
        return $this->andWhere(["<>", "status", Order::STATUS_PACKING]);
    }

    public function notStockOut(): OrderQuery
    {
        return $this->andWhere(["<>", "status", Order::STATUS_STOCK_OUT]);
    }

    public function unDelete(): OrderQuery
    {
        return $this->andWhere(["<>", "order.status", Order::STATUS_DELETE]);
    }

    public function notWattingShipper(): OrderQuery
    {
        return $this->andWhere(["<>", "order.status", Order::STATUS_WATING_SHIPPER]);
    }

    public function typeOrderNormal(): OrderQuery
    {
        return $this->andWhere(["order.type" => Order::TYPE_ORDER_NORMAL]);
    }

    public function typeOrderShipper(): OrderQuery
    {
        return $this->andWhere(["order.type" => Order::TYPE_ORDER_SHIPPER]);
    }

    public function pending(): OrderQuery
    {
        return $this->andWhere(["not in", "order.status", [
            Order::STATUS_DONE,
            Order::STATUS_RETURN,
            Order::STATUS_CANCEL,
            Order::STATUS_DELETE,
            Order::STATUS_WATING_SHIPPER
        ]]);
    }

    public function stop(): OrderQuery
    {
        return $this->andWhere(["order.status" => [
            Order::STATUS_DONE,
            Order::STATUS_CANCEL,
            Order::STATUS_RETURN,
            Order::STATUS_WATING_SHIPPER
        ]]);
    }

    public function channelPos(): OrderQuery
    {
        return $this->andWhere(["channel" => Order::CHANNEL_POS]);
    }

    public function channleWebsite(): OrderQuery
    {
        return $this->andWhere(["channel" => Order::CHANEL_WEBSITE]);
    }

    /**
     * @inheritdoc
     * @return Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
