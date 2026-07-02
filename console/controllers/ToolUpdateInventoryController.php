<?php

namespace console\controllers;

use api\modules\v1\admin\product\models\form\InitProductInventory;
use api\modules\v1\admin\product\models\form\ProductForm;
use common\models\ProductVariant;
use yii\console\Controller;

class ToolUpdateInventoryController extends Controller
{
    public function actionRun()
    {
        $products = ProductForm::find()->all();
        foreach ($products as $product) {
            $variants = ProductVariant::find()->where(["product_id" => $product->id])->all();
            foreach ($variants as $variant) {
                $productInventory = new InitProductInventory([
                    "product_id" => $product->id,
                    "product_variant_id" => $variant->id,
                    "unit_price" => $variant->unit_price,
                    "sll_price" => $variant->sll_price,
                    "quantity" => "100000",
                    "inventory_id" => 1,
                ]);
                $productInventory->variant = $variant;
                $productInventory->initInventory();
            }
        }
    }
}