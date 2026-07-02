<?php

namespace api\modules\v1\admin\inventory\models\form;

use api\modules\v1\admin\inventory\models\InventoryIssueItem;
use common\models\ProductInventory;
use common\models\ProductVariant;

class InventoryIssueItemForm extends InventoryIssueItem
{

    public $issue;

    public function rules()
    {
        return [
            [["product_id", "product_variant_id", "inventory_issue_id", "number_inventory", "quantity"], "required"],
            ["product_variant_id", "productVariantValidator"],
            ["number_inventory", "numberInventoryValidator"],
            [["number_inventory", "quantity"], "integer", "min" => 1],
            ["quantity", "quantityValidator"]
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

    public function quantityValidator($attribute)
    {
        if ($this->$attribute > $this->number_inventory) {
            $this->addError($attribute, "Invalid Quantity > Inventory");
        }
    }


    public function numberInventoryValidator($attribute)
    {
        $productInventory = ProductInventory::find()
            ->where(["product_variant_id" => $this->product_variant_id])
            ->andWhere(["inventory_id" => $this->issue->inventory_id])
            ->one();
        if (!$productInventory) {
            $this->addError($attribute, "{$this->product_variant_id} Product Inventory not found");
            return false;
        }
        $numberInventory = $productInventory->available;
        if ($numberInventory != $this->$attribute) {
            $this->addError($attribute, "invalid. Only accept: {$numberInventory}");
            return false;
        }
        return true;
    }
}
