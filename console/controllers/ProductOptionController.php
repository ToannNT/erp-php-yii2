<?php

namespace console\controllers;

use api\modules\v1\admin\product\models\Product;
use yii\console\Controller;

class ProductOptionController extends Controller
{
    public function actionIndex()
    {
        $products = Product::find()->all();
        foreach ($products as $product) {

        }
    }
}