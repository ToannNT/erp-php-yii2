<?php

namespace common\components\inventory;

use Yii;
use common\models\InventoryReceipt;
use common\models\ProductInventory;
use yii\base\Component;
use common\models\ProductVariant;

/**
 * Class Product
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\components\inventory
 */
class Product extends Component
{
    /**
     * @param $product_variant_id
     * @param $inventory_id
     * @param $incoming
     * @param int $incoming_old
     */
    public function incoming($product_variant_id, $inventory_id, $incoming, $incoming_old = 0)
    {
        // var_dump("in coming");
        // die;
        $productInventory = ProductInventory::find()->where(['product_variant_id' => $product_variant_id, 'inventory_id' => $inventory_id])->one();
        if (!$productInventory) {
            // get product_id by product variant
            $productVariant = ProductVariant::findOne([
                "id" => $product_variant_id
            ]);
            $productInventory = new ProductInventory([
                'product_variant_id' => $product_variant_id,
                'product_id' => $productVariant->product_id,
                'inventory_id' => $inventory_id,
                'quantity' => 0,
                'unit_price' => $productVariant->unit_price,
                'sll_price'=> $productVariant->sll_price,
                'available' => 0
            ]);
        }
        $productInventory->incoming = $productInventory->incoming + $incoming - $incoming_old;
        $productInventory->save(false);
        return;
    }

    /**
     * @param $product_variant_id
     * @param $inventory_id
     * @param $on_way
     */
    public function onWay($product_variant_id, $inventory_id, $on_way)
    {
        // var_dump("on Way");
        // die;
        $productInventory = ProductInventory::find()->where(['product_variant_id' => $product_variant_id, 'inventory_id' => $inventory_id])->one();
        if ($productInventory) {
            $productInventory->on_way = $productInventory->on_way + $on_way;
            $productInventory->committed = $productInventory->committed - $on_way;
            $productInventory->save(false);
        }

        return;
    }

    /**
     * @param $product_variant_id
     * @param $inventory_id
     * @param $on_way
     */
    public function deleteOnWay($product_variant_id, $inventory_id, $on_way)
    {
        var_dump("delete onWay");
        die;
        $productInventory = ProductInventory::find()->where(['product_variant_id' => $product_variant_id, 'inventory_id' => $inventory_id])->one();
        if ($productInventory) {
            $productInventory->on_way = $productInventory->on_way - $on_way;
            $productInventory->available = $productInventory->available + $on_way;
            $productInventory->save(false);
        }

        return;
    }

    /**
     * @param $product_variant_id
     * @param $inventory_id
     * @param $committed
     * @param $committed_old
     */
    public function committed($product_variant_id, $inventory_id, $committed, $committed_old = 0)
    {
        // var_dump("commit");
        // die;
        $productInventory = ProductInventory::find()
            ->where(['product_variant_id' => $product_variant_id, 'inventory_id' => $inventory_id])
            ->one();
        if ($productInventory) {
            $productInventory->committed = $productInventory->committed + $committed - $committed_old;
            $productInventory->available = $productInventory->available - $committed + $committed_old;
            $productInventory->save(false);
        }
        return;
    }

    /**
     * @param $product_variant_id
     * @param $inventory_id
     * @param $received
     */
    public function received($product_variant_id, $inventory_id, $received)
    {
        var_dump("received");
        die;
        $productInventory = ProductInventory::find()->where(['product_variant_id' => $product_variant_id, 'inventory_id' => $inventory_id])->one();
        if ($productInventory) {
            $productInventory->quantity = $productInventory->quantity + $received;
            $productInventory->available = $productInventory->available + $received;
            $productInventory->incoming = $productInventory->incoming - $received;
            $productInventory->save(false);
        }

        return;
    }

    /**
     * @param $product_variant_id
     * @param $inventory_id
     * @param $stock_out
     */
    public function stockOut($product_variant_id, $inventory_id, $stock_out)
    {
        $productInventory = ProductInventory::find()->where(['product_variant_id' => $product_variant_id, 'inventory_id' => $inventory_id])->one();
        if ($productInventory) {
            $productInventory->quantity = $productInventory->quantity - $stock_out;
            $productInventory->committed = $productInventory->committed - $stock_out;
            $productInventory->save(false);
        }

        return;
    }
}
