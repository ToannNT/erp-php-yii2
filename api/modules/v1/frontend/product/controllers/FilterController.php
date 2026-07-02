<?php

namespace api\modules\v1\frontend\product\controllers;

use api\modules\v1\frontend\product\models\Product;
use api\modules\v1\frontend\product\models\ProductVariant;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product\models\FilterModel;

class FilterController extends Controller
{
    public function actionIndex(): array
    {
        $filter = new FilterModel();
        $filter->load(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, [
            "product_options" => $filter->getProductOption(),
            "price" => $filter->getPrice(),
            "brands" => $filter->getBrands(),
            "categories" => $filter->getCategories()
        ]);
    }


    public function actionFilterInformation(): array
    {
        $fields = [];
        $maxPrice = 0;
        $minPrice = 0;

        $listProducts = Yii::$app->cache->getOrSet('product_variants', function () {
            return ProductVariant::find()->active()->all();
        }, 1800);

        foreach ($listProducts as $product) {
            if ($product->unit_price > $maxPrice) {
                $maxPrice = $product->unit_price;
            }
            if ($product->unit_price < $minPrice) {
                $minPrice = $product->unit_price;
            }
            if (!empty($product->meta_field[0])) {
                foreach ($product->meta_field as $field) {
                    $value = [
                        'value' => $field['value'],
                        'additional_data' => $field['additional_data']
                    ];
                    if (empty($fields[$field['key']])) {
                        $fields[$field['key']] = [
                            'key' => $field['key'],
                            'name' => $field['name'],
                            'slug' => $field['slug'],
                            'values' => [],
                        ];
                    }
                    $fields[$field['key']]['values'][$field['slug']] = $value;
                }
            }
        }
        return ResponseBuilder::responseJson(true,
            ["fields" => $fields, "max_price" => $maxPrice, "min_price" => $minPrice]);
    }
}