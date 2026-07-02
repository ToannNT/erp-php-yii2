<?php

namespace api\modules\v1\frontend\product\models;

use Yii;
use yii\base\Model;
use common\models\Brand;
use common\models\Category;

class FilterModel extends Model
{
    public $category_id;
    public $brand_id;
    public $maxPrice;
    public $minPrice;

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            ["category_id", "safe"],
            ["brand_id", "safe"]
        ];
    }

    public function getProductOption()
    {
        $this->minPrice = 2999999999;
        $this->maxPrice = 0;
        $products = Product::find()
            ->select(["product_options", "unit_price"])
            ->andFilterWhere(["category_id" => $this->category_id, "brand_id" => $this->brand_id])
            ->active()
            ->all();
        $result = [];
        foreach ($products as $product) {
            if ($product->unit_price > $this->maxPrice) {
                $this->maxPrice = $product->unit_price;
            }
            if ($product->unit_price < $this->minPrice) {
                $this->minPrice = $product->unit_price;
            }
            foreach ($product->product_options as $product_option) {
                if (!isset($result[$product_option["key"]])) {
                    $result[$product_option["key"]] = array_merge($product_option, [
                        "values_slug" => [],
                        "values" => [],
                    ]);
                }
                foreach ($product_option["values"] as $optionValue) {
                    if (!in_array($optionValue["slug"], $result[$product_option["key"]]["values_slug"])) {
                        $result[$product_option["key"]]["values_slug"][] = $optionValue["slug"];
                        $result[$product_option["key"]]["values"][] = $optionValue;
                    }
                }
            }
        }
        return $result;
    }

    public function getBrands()
    {
        return Brand::find()->select(["brand.id", "name", "slug"])
            ->andFilterWhere(["brand.id" => $this->brand_id, "category_brand.category_id" => $this->category_id])
            ->joinWith("categoryBrands", false)
            ->active()
            ->asArray()
            ->all();
    }

    public function getCategories()
    {
        return Category::find()
            ->select(["id", "name", "slug"])
            ->andFilterWhere(["id" => $this->category_id])
            ->active()
            ->asArray()
            ->all();
    }

    public function getPrice()
    {
        return [
            "max_price" => $this->maxPrice,
            "min_price" => $this->minPrice
        ];
    }
}