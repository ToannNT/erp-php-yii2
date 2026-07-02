<?php

namespace api\modules\v1\admin\inventory\models\form;

use api\modules\v1\admin\inventory\models\StocktakingItem;
use common\models\ProductInventory;
use common\models\ProductVariant;

class StocktakingItemForm extends StocktakingItem
{

    public $stocktaking;

    public function rules()
    {
        return [
            [["stocktaking_id", "product_id", "product_variant_id"], "required"],
            [["number_inventory", "number_difference", "number_adjustment"], "integer"],
            [["number_inventory", "number_difference", "number_adjustment"], "filter", "filter" => function ($value) {
                return intval($value);
            }],
            [["reason"], "string"],
            [["product_variant_id"], "productVariantValidator"],
            ["number_adjustment", "integer", "min" => 0],
            ["number_inventory", "numberInventoryValidator"],
            ["number_difference", "numberDifferenceValidator"]
        ];
    }

    public function productVariantValidator($attribute)
    {
        $product = ProductVariant::find()
            ->where(["product_id" => $this->product_id])
            ->andWhere(["id" => $this->product_variant_id])
            ->unDelete()
            ->one();
        if (!$product) {
            $this->addError($attribute, "Product invalid");
        }
    }

    public function numberInventoryValidator($attribute)
    {
        $productInventory = ProductInventory::find()
            ->where(["product_variant_id" => $this->product_variant_id])
            ->andWhere(["inventory_id" => $this->stocktaking->inventory_id])
            ->one();
        if (!$productInventory) {
            $this->addError($attribute, "Product Inventory not found");
            return false;
        }
        $numberInventory = $productInventory->available;
        if ($numberInventory != $this->$attribute) {
            $this->addError($attribute, "invalid. Only accept: {$numberInventory}");
            return false;
        }
        return true;
    }

    public function numberDifferenceValidator($attribute)
    {
        $number_difference = $this->number_adjustment - $this->number_inventory;
        if ($number_difference != $this->number_difference) {
            $this->addError($attribute, "invalid. Only accept: {$number_difference}");
        }
    }
}
