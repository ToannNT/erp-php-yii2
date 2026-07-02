<?php

namespace common\models;

use Yii;
use \common\models\base\OrderItem as BaseOrderItem;
use yii\helpers\ArrayHelper;

/**
 * Class OrderItem
 * @property ProductVariant $productVariant;
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class OrderItem extends BaseOrderItem
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = -99;

    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules(): array
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }


    /**
     * @return yii\db\ActiveQuery
     */
    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ['id' => 'product_variant_id']);
    }

    /**
     * @throws yii\base\InvalidConfigException
     */
    public function getProductSuppliers()
    {
        return $this->hasMany(ProductSupplier::class, ["product_id" => "product_id"])
            ->viaTable("product_variant", ["id" => "product_variant_id"]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function getSuppliers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Supplier::className(), ["id" => "supplier_id"])
            ->viaTable("product_supplier", ["product_id" => "product_id"]);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ["id" => "order_id"]);
    }

    public function getOrderReturns()
    {
        return $this->hasMany(OrderReturn::className(), ["order_id" => "id"])
            ->via("order");
    }

    public function getOrderReturnItems()
    {
        return $this->hasMany(OrderReturnItem::className(), ["product_variant_id" => "product_variant_id"]);
    }

    public function calculate(): bool
    {
        $this->tax_price = 0;
        $this->sub_total = $this->unit_price * $this->quantity;
        $this->total_price = $this->sub_total - $this->discount_price * $this->quantity + $this->tax_price;
        $this->total_price = $this->total_price <= 0 ? 0 : $this->total_price;
        return true;
    }
}
