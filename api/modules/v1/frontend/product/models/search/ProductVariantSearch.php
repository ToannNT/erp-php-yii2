<?php

namespace api\modules\v1\frontend\product\models\search;

class ProductVariantSearch extends \common\models\ProductVariant
{
    public function rules(): array
    {
        return [
            [["slug", "id"], "safe"]
        ];
    }

    public function fields()
    {
        return array_merge([
            "name",
            "product_id",
            "unit_price",
            "images",
            "slug",
            "color_id",
            "colors" => "colors",
            "category" => function () {
                return $this->product->categoryName;
            },
            "brand" => function () {
                return $this->product->brandName;
            },
            "rating" => "rating",
            "product_meta" => function () {
                return $this->product->productMeta;
            },
            "product_property" => "productProperty"
        ]);
    }

    public function searchFind($params)
    {
        $query = self::find()
            ->joinWith("product")
            ->andWhere(["allow_sell" => 1]);
        $this->load($params, "");
        if (!$this->validate()) {
            return $query->one();
        }
        $query->andFilterWhere([
            "product_variant.id" => $this->id,
            "product_variant.slug" => $this->slug
        ]);
        return $query->one();
    }

    public function search()
    {
    }
}
