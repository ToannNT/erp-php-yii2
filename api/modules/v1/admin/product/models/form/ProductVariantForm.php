<?php

namespace api\modules\v1\admin\product\models\form;

use common\models\InventoryHistory;
use common\models\ProductInventory;
use Exception;
use Yii;
use yii\behaviors\SluggableBehavior;
use common\validators\IsArrayValidator;
use api\modules\v1\admin\product\models\ProductVariant;
use common\models\Product;

class ProductVariantForm extends ProductVariant
{
    public $inventories;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["slug"] =
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
            ];
        return $behaviors;
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @return bool|void
     * Trigger update unit price and sll price in product inventories by product inventory
     */

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            return true;
        }
        if (!isset($changedAttributes["unit_price"], $changedAttributes["sll_price"])) {
            return false;
        }
        if (
            $changedAttributes["unit_price"] != $this->unit_price ||
            $changedAttributes["sll_price"] != $this->sll_price
        ) {
            /**
             * @var ProductInventory $productInventory
             */
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($this->productInventories as $productInventory) {
                    $productInventory->unit_price = $this->unit_price;
                    $productInventory->sll_price = $this->sll_price;
                    $productInventory->save(false);
                }
                $transaction->commit();
            } catch (Exception $exception) {
                $transaction->rollBack();
            }
        }
    }

    public
    function rules()
    {
        return [
            [["name", "sku", "product_id"], "required"],
            [["name", "sku"], "unique", "filter" => [
                "!=",
                "status",
                ProductVariant::STATUS_DELETE
            ]],
            [["product_id"], "exist", 'targetClass' => Product::class, "filter" => [
                "<>",
                "status",
                Product::STATUS_DELETE
            ], 'targetAttribute' => ['product_id' => 'id']],
            [["import_price", "unit_price", "sll_price", "compare_price", "weight"], "number", "min" => 0],
            [["import_price", "unit_price", "sll_price", "compare_price", "weight"], "default", "value" => 0],
            [["weight_type"], "in", "range" => ["kg", "g"]],
            [["status"], "default", "value" => ProductVariant::STATUS_ACTIVE],
            [["dimension", "barcode"], "string"],
            [["inventories"], IsArrayValidator::class],
            ["inventories", "default", "value" => []],
            [["images", "meta_field"], "default", "value" => []],
            ["inventory_management", "default", "value" => ProductVariant::INVENTORY_MANAGEMENT_IN_ACTIVE],
        ];
    }

    /**
     * @throws Exception
     */
    public function initProductInventories(): bool
    {
        foreach ($this->inventories as $inventory) {
            if (!$this->initProductInventory($inventory)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function initProductInventory($inventoryParams): bool
    {
        $productInventory = new InitProductInventory();
        /**
         * Change algorithm get unit_price, sll_price product variant to product_inventory
         */
        $productInventory->load(array_merge($inventoryParams, [
            "unit_price" => $this->unit_price,
            "sll_price" => $this->sll_price
        ]));
        $productInventory->variant = $this;
        if (!$productInventory->save()) {
            $summary = $productInventory->getErrorSummary(true);
            $this->addError("inventories", $summary ? implode('; ', $summary) : "Không lưu được tồn kho.");
            return false;
        }
        $inventoryHistory = new InventoryHistory([
            "inventory_id" => $productInventory->inventory_id,
            "inventory" => $productInventory->available,
            "created_by" => Yii::$app->user->getId(),
            "action" => InventoryHistory::ACTION_INIT_VARIANT,
            "change_quantity" => "+ $productInventory->available",
            "product_id" => $this->product_id,
            "product_variant_id" => $productInventory->product_variant_id
        ]);
        return $inventoryHistory->save(false);
    }
}
