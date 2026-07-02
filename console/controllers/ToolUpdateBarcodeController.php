<?php

namespace console\controllers;

use common\models\Product;
use common\models\ProductVariant;
use yii\console\Controller;

class ToolUpdateBarcodeController extends Controller
{
    public function actionRunProductVariant()
    {
        $productVariants = ProductVariant::find()->all();
        foreach ($productVariants as $productVariant) {
            $newBarcode = rand(100000000, 999999999) . $productVariant->id;
            $productVariant->barcode = $newBarcode;
            if (!$productVariant->save(false)) {
                echo "Error";
                break;
            }
        }
        echo "done";
    }

    public function actionRunProduct()
    {
        $products = Product::find()->all();
        foreach ($products as $product) {
            $newBarcode = rand(100000000, 999999999) . $product->id;
            $product->bar_code = $newBarcode;
            if (!$product->save(false)) {
                echo "Error";
                break;
            }
        }
        echo "done";
    }
    
}