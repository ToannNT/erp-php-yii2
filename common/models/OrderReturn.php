<?php

namespace common\models;

use common\behaviors\JsonBehavior;
use Yii;
use \common\models\base\OrderReturn as BaseOrderReturn;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_return".
 */
class OrderReturn extends BaseOrderReturn
{
    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_PRICE = 2;
    const STATUS_ACTIVE = 1;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["progress_status", "data_delivery_fee", "other_fee"]
        ];
        return $behaviors;
    }

    public function getOffice()
    {
        return $this->hasOne(Office::class, ["id" => "office_id"]);
    }

    public function getClient()
    {
        return $this->hasOne(Customer::className(), ["id" => "client_id"]);
    }

    public function getInventory()
    {
        return $this->hasOne(Inventory::class, ["id" => "inventory_id"]);
    }

    public function getOrderReturnItems()
    {
        return $this->hasMany(OrderReturnItem::className(), ["order_return_id" => "id"]);
    }

    public function getOrders()
    {
        return $this->hasMany(Order::className(), ["id" => "order_id"]);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ["id" => "order_id"]);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ["id" => "created_by"]);
    }

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'TH' . $tmp;
    }

    public function createOrderOrderReturn()
    {
        (new OrderOrderReturn([
            "status" => OrderOrderReturn::STATUS_ACTIVE,
            "order_id" => $this->order_id,
            "order_return_id" => $this->id
        ]))->save(false);
    }
}
