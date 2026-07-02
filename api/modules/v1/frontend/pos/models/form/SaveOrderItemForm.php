<?php

namespace api\modules\v1\frontend\pos\models\form;

use Yii;
use common\models\ProductInventory;
use common\models\Order;
use common\models\OrderItem;

class SaveOrderItemForm extends OrderItem
{
    public $inventory_id;
    public $discount_value;
    public $discount_type;
    public $discount_reason;
    public $tax_value = 0;
    public $tax_reason;

    public function fields(): array
    {
        return [
            "order_id",
            "quantity",
            "product_variant_id",
            "unit_price",
            "note",
            "discount_type",
            "discount_value",
            "total_price",
            "discount_price",
            "tax_price",
            "tax_value"
        ];
    }

    public function rules(): array
    {
        return [
            [["order_id", "quantity", "product_variant_id"], "required"],
            [["order_id"], "checkOrderRule"],
            [["quantity", "discount_type"], "integer", "min" => 1],
            [["unit_price", "discount_value"], "number"],
            [["note", "tax_reason", "discount_reason", "name"], "string"],
            [["product_variant_id"], "checkAvailableInventory"],
            [["discount_value"], "checkDiscountTypeRule"],
            [["tax_value"], "number", "max" => 100]
        ];
    }

    public function calculateDiscount()
    {
        $discount = 0;
        if ($this->discount_type) {
            if ($this->discount_type == Order::DISCOUNT_PRICE) {
                $discount = ((float)$this->discount_value / 100) * $this->unit_price;
            } else {
                $discount = (float)$this->discount_value;
            }
        }
        $this->discount_price = $discount * $this->quantity;
    }


    public function saveCalculated()
    {
        $this->data_discount = json_encode([
            "discount_value" => $this->discount_value,
            "discount_type" => $this->discount_type,
            "discount_price" => $this->discount_price,
            "discount_reason" => $this->discount_reason
        ]);
        $this->data_tax = json_encode([
            "tax_value" => $this->tax_value,
            "tax_price" => $this->tax_price,
            "tax_reason" => $this->tax_reason
        ]);
        $this->status = self::STATUS_ACTIVE;
        $this->save();
    }

    /**
     *
     * @param string $attribute
     * @author khuongdev2001
     */
    public function checkOrderRule(string $attribute)
    {
        $order = Order::find()->pending()
            ->andWhere(["id" => $this[$attribute]])
            ->andWhere(["office_id" => array_column(Yii::$app->user->identity->offices, "id")])
            ->andWhere(["status" => [Order::STATUS_ORDER]])
            ->one();
        if (!$order) {
            $this->addError($attribute, "not found");
        }
    }

    /**
     * @param string $attribute
     * @return bool|void
     * @author khuongdev2001
     */
    public function checkAvailableInventory(string $attribute)
    {
        $inventory_ids = array_column(Yii::$app->user->identity->inventorys, "id");
        $productInventory = ProductInventory::find()
            ->where(["inventory_id" => $inventory_ids])
            ->andWhere(["product_variant_id" => $this->product_variant_id])
            ->select(["SUM(`available`) as total_available", "unit_price", "product_id"])
            ->asArray()
            ->one();
        if (!$productInventory) {
            return $this->addError($attribute, "product_variant_id not found");
        }
        if ($productInventory["total_available"] < $this->quantity) {
            $this->addError($attribute, "quantity is not enough, quantity inventory: {$productInventory['total_available']}");
        }
        $this->unit_price = $this->unit_price ?: $productInventory["unit_price"];
        $this->product_id = $productInventory["product_id"];
        return true;
    }

    /**
     * @param string $attribute
     * @return void
     * @author khuongdev2001
     */
    public function checkDiscountTypeRule(string $attribute)
    {
        if (
            $this->discount_type == Order::DISCOUNT_PRICE
            && $this->discount_value > 100
        ) {
            $this->addError($attribute, "invalid");
        }
    }
}
