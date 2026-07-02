<?php

namespace common\components\inventory;

use Yii;
use common\models\InventoryHistory;
use yii\base\Component;

/**
 * Class History
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\components\inventory
 */
class History extends Component
{
    /**
     * @param $action
     * @param $quantity
     * @param $inventory
     * @param $inventory_id
     * @param $product_variant_id
     * @param $voucher_code
     * @param $link_detail
     */
    public function create($action, $quantity, $inventory, $inventory_id, $product_variant_id, $voucher_code, $link_detail = null)
    {
        $inventoryHistory = new InventoryHistory([
            'created_by' => Yii::$app->user->identity->id,
            'action' => $action,
            'change_quantity' => $quantity,
            'inventory' => $inventory,
            'inventory_id' => $inventory_id,
            'product_variant_id' => $product_variant_id,
            'voucher_code' => $voucher_code,
            'link_detail' => $link_detail,
        ]);

        $inventoryHistory->save(false);
        return;
    }


    /**
     * @param $quantity
     * @param $inventory_id
     * @param $product_variant_id
     * @param $voucher_code
     */
    public function update($quantity, $inventory_id, $product_variant_id, $voucher_code)
    {
        $inventoryHistory = InventoryHistory::find()->where([
            'inventory_id' => $inventory_id,
            'product_variant_ai' => $product_variant_id,
            'voucher_code' => $voucher_code,
        ])->one();
        if ($inventoryHistory) {
            $inventoryHistory->change_quantity = '+' . $quantity;
            $inventoryHistory->save(false);
        }

        return;
    }
}