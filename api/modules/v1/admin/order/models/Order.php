<?php

namespace api\modules\v1\admin\order\models;

use api\modules\v1\frontend\pos\models\OrderShip;
use common\models\OrderDiscount;
use yii\db\Query;
use common\models\Order as BaseModel;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class Order extends BaseModel
{
    const SCENARIO_CANCEL = "scenario_cancel";
    const SCENARIO_ADD_PROMOTION = "scenario_promotion";
    const MANUAL_STATUS_TRANSITIONS = [
        self::STATUS_ORDER          => [self::STATUS_APPROVED, self::STATUS_CANCEL],
        self::STATUS_APPROVED       => [self::STATUS_WATING_SHIPPER, self::STATUS_CANCEL],
        self::STATUS_WATING_SHIPPER => [self::STATUS_DONE, self::STATUS_CANCEL],
        self::STATUS_DONE           => [],
        self::STATUS_CANCEL         => [],
    ];

    public function canChangeStatusTo($status): bool
    {
        $allowed = self::MANUAL_STATUS_TRANSITIONS[$this->status] ?? [];
        return in_array((int)$status, $allowed, true);
    }

    public function fields()
    {
        $fields = [
            "id",
            "code",
            "channel",
            "quantity",
            "office" => "office",
            "office_id",
            "inventory" => "inventory",
            "inventory_id",
            "client" => "client",
            "created_by" => function () {
                return $this->createdBy?->username;
            },
            "type",
            "discount",
            "sum_discount_product" => function () {
                return (float)$this->sumDiscountPriceOrderItems;
            },
            "sum_sub_total_product" => function () {
                return (float)$this->sumSubTotalOrderItems;
            },
            "data_discount" => function ($model) {
                return $this->getDataDiscount();
            },
            "total_price",
            "tax_price",
            "data_tax",
            "delivery_fee",
            "data_delivery_fee",
            "payments",
            "data_payments",
            "payment_methods" => "mapOrderPaymentMethods",
            "status",
            "progress_status",
            "shipping_address",
            "order_address",
            "price_policy",
            "note",
            "tags",
            "return_note",
            "created_at",
            "done_at",
            "updated_at",
        ];
        if ($this->type === self::TYPE_ORDER_SHIPPER) {
            $fields = array_merge($fields, [
                "order_ship" => "orderShip"
            ]);
        }
        return $fields;
    }

    public function extraFields(): array
    {
        return [
            "order_items" => function () {
                return $this->getOrderItemsByOrder($this)->all();
            },
        ];
    }

    public function getDataDiscount()
    {
        $promotionData = OrderDiscount::find()->where(["type" => OrderDiscount::TYPE_PROMOTION, "order_id" => $this->id])
            ->select(["id", "code", "discount_type", "discount_value", "discount_price", "title"])
            ->all();
        return [
            "promotion_data" => $promotionData
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getPromotion()
    {
        return parent::getPromotion()->addSelect(["id", "code"]);
    }

    public function getOrderItemsByOrder(Order $order)
    {
        $relationShip = $this->hasMany(OrderItem::class, ["order_id" => "id"])
            ->joinWith("productVariant");
        if ($order->channel == BaseModel::CHANEL_WEBSITE) {
            $relationShip->joinWith(["productInventory" => function (Query $query) use ($order) {
                $query->andWhere(["product_inventory.inventory_id" => $order->inventory_id]);
            }]);
        }
        $relationShip->orderBy(["order_item.id" => SORT_DESC]);
        return $relationShip;
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


    public function calculateOrder()
    {
        $this->calculateBeforePromotion();
        if ($this->calculateAfterPromotion()) {
            return false;
        }
    }

    public function calculateBeforePromotion()
    {
        $this->total = 0;
        foreach ($this->giftCards as $giftCard) {
            $this->total += $giftCard->unit_price;
        }
    }

    public function calculateAfterPromotion()
    {
        // get promotion history
        $promotionHistory = $this->promotionHistory();
        $discount_price = 0;
        switch ($promotionHistory->type) {
            case "percent":
                $discount_price = $this->total * ($promotionHistory->discount_value / 100);
                break;
            case "usd":
                $discount_price = $promotionHistory->discount_value;
                break;
        }
        $this->total -= $discount_price;
        $promotionHistory->total_discount = $this->total;
    }
}
