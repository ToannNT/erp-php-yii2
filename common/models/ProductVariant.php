<?php

namespace common\models;

use Yii;
use \common\models\base\ProductVariant as BaseProductVariant;
use yii\helpers\ArrayHelper;

/**
 * Class ProductVariant
 * @property ProductInventory[] $productInventory
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class ProductVariant extends BaseProductVariant
{
    const STATUS_DELETE = -99;
    const INVENTORY_MANAGEMENT_ACTIVE = 1;
    const INVENTORY_MANAGEMENT_IN_ACTIVE = 0;

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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductInventory()
    {
        return $this->hasMany(ProductInventory::class, ['product_variant_id' => 'id']);
    }

    public function getProductInventories()
    {
        return $this->hasMany(ProductInventory::class, ['product_variant_id' => 'id']);
    }

    public function getInventorys()
    {
        return $this->hasMany(Inventory::class, ["id" => "inventory_id"])
            ->via("productInventory");
    }


    public function getProduct($selects = [])
    {
        return $this->hasOne(Product::class, ["id" => "product_id"])
            ->andOnCondition(["<>", "product.status", Product::STATUS_DELETE])
            ->select($selects);
    }

    public function getColors($selects = ["id", "name", "value"])
    {
        $product_variants = ProductVariant::find()->where(["name" => $this->name])->all();
        return Color::find()->where(["in", "id", array_column($product_variants, "color_id")])
            ->select($selects)
            ->all();
    }

    public function getRating(): float
    {
        return round($this->hasOne(Comment::class, ["module_id" => "product_id"])
            ->where(["type" => Comment::TYPE_PRODUCT])
            ->andWhere(["status" => Comment::STATUS_ACTIVE])
            ->average("rating"), 1);
    }

    public function getProductProperty()
    {
        return $this->hasOne(ProductProperty::class, ["id" => "product_variant_id"]);
    }

    public function getSuppliers()
    {
        return $this->hasMany(Supplier::class, ["id" => "supplier_id"])
            ->andWhere(["{{supplier}}.status" => Supplier::STATUS_ACTIVE])
            ->viaTable("product_supplier", ["product_id" => "product_id"]);
    }

    public function getQuantity()
    {
        return $this->hasMany(ProductInventory::class, ['product_variant_id' => 'id'])
            ->where(['!=', 'status', ProductVariant::STATUS_DELETE])
            ->sum('quantity');
    }


    public function getAvailable()
    {
        return $this->hasMany(ProductInventory::class, ['product_variant_id' => 'id'])
            ->where(['!=', 'status', ProductVariant::STATUS_DELETE])
            ->sum('available');
    }
}
