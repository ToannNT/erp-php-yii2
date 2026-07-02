<?php

namespace api\modules\v1\frontend\pos\models;

use Yii;

class OrderItem extends \common\models\OrderItem
{
    public function fields(): array
    {
        return [
            "id",
            "order_id",
            "product_id",
            "product_variant_id",
            "unit_price",
            "quantity",
            "quantity_return",
            "total_price",
            "sub_total",
            "discount_price",
            "weight" => function () {
                return empty($this->productVariant->weight) ? null : $this->productVariant->weight;
            },
            "product_name" => function () {
                return empty($this->name) ? $this->productVariant->name : $this->name;
            },
            "images" => function () {
                return empty($this->productVariant->images) ? null : json_decode($this->productVariant->images);
            },
            "sku" => function () {
                return empty($this->productVariant->sku) ? null : $this->productVariant->sku;
            },
            "note",
            "available" => "available"
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ["id" => "order_id"])->orderBy(["id" => SORT_DESC]);
    }

    public function getAvailable()
    {
        return $this->hasMany(ProductInventory::class, ['product_variant_id' => 'product_variant_id'])
            ->andWhere(["inventory_id" => array_column(Yii::$app->user->identity->inventorys, "id")])
            ->sum('available');
    }
}
