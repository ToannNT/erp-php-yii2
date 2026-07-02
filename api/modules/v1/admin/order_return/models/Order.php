<?php

namespace api\modules\v1\admin\order_return\models;

use api\modules\v1\admin\order\models\OrderItem;
use api\modules\v1\frontend\pos\models\OrderShip;
use common\models\Order as BaseOrder;
use yii\db\Expression;

class Order extends BaseOrder
{

    public function fields()
    {
        $fields = [
            "id",
            "code",
            "channel",
            "quantity",
            "type",
            "inventory" => "inventory",
            "office" => "office",
            "office_id",
            "client_id",
            "total_price",
            "discount",
            "payments",
            "created_by" => function () {
                return empty($this->createdBy) ?: $this->createdBy->username;
            },
            "client" => "client",
            "order_items" => "orderItems",
            "created_at",
            "updated_at"
        ];
        if ($this->type == self::TYPE_ORDER_SHIPPER) {
            $fields = array_merge($fields, [
                "order_ship" => "orderShip"
            ]);
        }
        return $fields;
    }

    public function formName()
    {
        return "";
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ["order_id" => "id"])
            ->onCondition([">", "order_item.quantity", new Expression("order_item.quantity_return")])
            ->joinWith("productVariant");
    }

    public function getInventory()
    {
        return parent::getInventory()->addSelect(["id", "name"]);
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }

    public function getClient()
    {
        return parent::getClient()->addSelect(["id", "name", "phone"]);
    }

    public function getOrderShip()
    {
        return $this->hasOne(OrderShip::class, ["order_id" => "id"])
            ->joinWith("shipper");
    }

}
