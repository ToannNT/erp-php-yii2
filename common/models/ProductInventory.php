<?php

namespace common\models;

use Yii;
use \common\models\base\ProductInventory as BaseProductInventory;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class ProductInventory
 * @property ProductVariant $productVariant
 * @property Inventory $inventory
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class ProductInventory extends BaseProductInventory
{

    const STATUS_DELETE = -99;

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

    public static function interactive($inventory, $product)
    {
        $model = self::find()->where([
            "product_variant_id" => $product["product_variant_id"],
            "inventory_id" => $inventory["inventory_id"]
        ]);
        if ($model->one()) {
            return $model->one();
        }
        $productVariant = ProductVariant::find()->where(["id" => $product["product_variant_id"]])->active()->one();
        $model = (new self([
            "product_id" => $product["product_id"],
            "product_variant_id" => $product["product_variant_id"],
            "inventory_id" => $inventory["inventory_id"],
            "available" => 0,
            "quantity" => 0,
            "incoming" => 0,
            "unit_price" => $productVariant->unit_price,
            "sll_price" => $productVariant->sll_price
        ]));
        $model->save(false);
        return $model;
    }

    public function checkAvailableCurrent($quantity)
    {
        if ($this->available < $quantity) {
            return false;
        }
        return $this;
    }

    public function addIncoming($quantity)
    {
        $this->incoming += $quantity;
        return $this;
    }

    public function addOnWay($quantity)
    {
        $this->on_way += $quantity;
        return $this;
    }

    public function addComitted($quantity)
    {
        $this->committed += $quantity;
        return $this;
    }

    public function setAvailable($quantity)
    {
        $this->available = $quantity;
        return $this;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function addQuantity($quantity)
    {
        $this->quantity += $quantity;
        return $this;
    }

    public function addAvailable($quantity)
    {
        $this->available += $quantity;
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ['id' => 'product_variant_id'])
            ->andOnCondition(["<>", "product_variant.status", Product::STATUS_DELETE]);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])
            ->andOnCondition(["<>", "product.status", Product::STATUS_DELETE]);
    }

    public function getProductVariants()
    {
        return $this->hasMany(ProductVariant::class, ['id' => 'product_variant_id'])
            ->andOnCondition(["<>", "product_variant.status", ProductVariant::STATUS_DELETE]);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->andOnCondition(["<>", "product.status", Product::STATUS_DELETE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(Inventory::class, ['id' => 'inventory_id'])
            ->andOnCondition(["inventory.status" => Inventory::STATUS_ACTIVE]);
    }

    public function getUserOffice()
    {
        return $this->hasOne(UserOffice::class, ["office_id" => "id"])
            ->viaTable("office", ["id" => "office_id"])
            ->viaTable("inventory", ["id" => "inventory_id"]);
    }

    public function getOffices()
    {
        return $this->hasMany(Office::class, ["id" => "office_id"])
            ->andOnCondition(["office.status" => Office::STATUS_ACTIVE])
            ->viaTable("inventory", ["id" => "inventory_id"]);
    }

    public function getOffice()
    {
        return $this->hasOne(Office::class, ["id" => "office_id"])
            ->andOnCondition(["office.status" => Office::STATUS_ACTIVE])
            ->viaTable("inventory", ["id" => "inventory_id"]);
    }


    public function getSuppliers()
    {
        return $this->hasMany(ProductSupplier::class, ["product_id" => "product_id"]);
    }
}
