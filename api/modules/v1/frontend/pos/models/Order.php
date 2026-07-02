<?php

namespace api\modules\v1\frontend\pos\models;

use common\components\inventory\Issue;
use common\models\Inventory;
use common\models\Order as OrderBase;
use common\models\OrderDiscount;
use common\models\Promotion;
use Exception;
use SamIT\Yii2\Components\Map;
use Yii;

class Order extends OrderBase
{
    public function fields()
    {
        $fields = [
            "id",
            "code",
            "office_id",
            "office" => "office",
            "status",
            "client" => "client",
            "total_price",
            'payments',
            "data_payments",
            "sum_discount_product" => function () {
                return (float)$this->sumDiscountPriceOrderItems;
            },
            "sum_sub_total_product" => function () {
                return (float)$this->sumSubTotalOrderItems;
            },
            "other_fee",
            "payment_methods" => "mapOrderPaymentMethods",
            "delivery_fee",
            "data_other_fee",
            "data_delivery_fee",
            "tax_price",
            "discount",
            "quantity",
            "channel",
            "type",
            "price_policy",
            "note",
            "created_at",
            "data_discount" => function ($model) {
                return $this->getDataDiscount();
            },
            "order_items" => "orderItems",
            "external_id"
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

    public function getDataDiscount()
    {
        $promotionData = OrderDiscount::find()->where(["type" => OrderDiscount::TYPE_PROMOTION, "order_id" => $this->id])
            ->select(["id", "code", "discount_type", "discount_value", "discount_price", "title"])
            ->all();
        return [
            "promotion_data" => $promotionData
        ];
    }

    public function getOrderShip()
    {
        return $this->hasOne(OrderShip::class, ["order_id" => "id"])
            ->joinWith("shipper");
    }

    public function getClient()
    {
        return parent::getClient()
            ->addSelect(["id", "name", "code", "phone", "email", "address_1", "province_code", "district_code", "ward_code"]);
    }

    public function getPromotion()
    {
        return parent::getPromotion();
    }

    public function getInventory()
    {
        return parent::getInventory()
            ->addSelect(["id", "name", "code", "office_id"]);
    }

    public function getOffice()
    {
        return parent::getOffice()
            ->addSelect(["id", "name", "address1", "address2", "contact_person_id", "province_code", "district_code", "ward_code"]);
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ["order_id" => "id"])
            ->orderBy(["order_item.id" => SORT_DESC]);
    }

    public function saveCalculated()
    {
        return $this->save();
    }


    /**
     * @throws yii\db\Exception|Exception
     * @author khuongdev2001
     * Here is method update quantity, available in product inventory
     * if a quantity order unconditional rollback quantity initial
     */
    public function updateInventory()
    {
        $inventory = Yii::$app->user->identity->inventoryFirst;
        $inventories = [];
        foreach ($this->orderItems as $orderItem) {
            $query = ProductInventory::find()
                //                ->where(["inventory_id" => array_column(Yii::$app->user->identity->inventorys, "id")])
                ->where(["inventory_id" => $inventory->id ?? null])
                ->andWhere(["product_variant_id" => $orderItem->product_variant_id])
                ->andWhere([">", "available", 0]);
            if ($orderItem->quantity > $query->sum("available"))
                throw new Exception("quantity invalid");
            $productInventorys = $query->all();
            $quantityRemaining = $orderItem->quantity;
            /** @var ProductInventory $productInventory */
            foreach ($productInventorys as $productInventory) {
                $available = $productInventory->available;
                $quantityRemaining -= $available;
                if ($quantityRemaining <= 0) {
                    $productInventory->available = abs($quantityRemaining);
                    $productInventory->quantity = abs($quantityRemaining);
                    $inventories[$productInventory->inventory_id]["items"][] = [
                        "quantity" => $available - abs($quantityRemaining),
                        "number_inventory" => $available,
                        "product_variant_id" => $productInventory->product_variant_id
                    ];
                    $productInventory->save(false);
                    break;
                }
                $inventories[$productInventory->inventory_id]["items"][] = [
                    "quantity" => $productInventory->available,
                    "number_inventory" => $available,
                    "product_variant_id" => $productInventory->product_variant_id
                ];
                $productInventory->available = 0;
                $productInventory->quantity = 0;
                $productInventory->save(false);
            }
            if ($quantityRemaining > 0)
                throw new Exception("quantity invalid");
        }
        $this->setInventoryIssues($inventories);
    }


    protected function setInventoryIssues($inventories)
    {
        $office_id = Yii::$app->user->identity->office->id;
        foreach ($inventories as $id => $issueItems) {
            /** @var ProductInventory $productInventory */
            /** @var Issue $inventoryIssueComponent */
            $inventoryIssue = new InventoryIssue([
                "inventory_id" => $id,
                "office_id" => $office_id,
                "order_id" => $this->id,
                "type" => InventoryIssue::TYPE_DELIVER,
                "status" => InventoryIssue::STATUS_DONE,
                "total_number" => 0,
                "created_by" => Yii::$app->user->identity->id
            ]);
            $inventoryIssue->issueItems = $issueItems["items"];
            $inventoryIssue->save(false);
        }
    }

    public function calculate()
    {
        $this->clearDiscountPromotion();
        $this->calculateSubTotal();
        $this->calculateDiscount();
        $this->resetDiscount();
        $this->calculateDeliveryFee();
        $this->calculateOtherFee();
        $this->calculatePayment();
        $this->resetPayments();
    }

    public function clearDelivery()
    {
        $this->data_other_fee = [];
        $this->data_delivery_fee = [];
    }

    public function getFirstInventory()
    {
        return $this->hasOne(Inventory::className(), ["office_id" => $this->office_id]);
    }
}
