<?php

namespace api\modules\v1\frontend\product\models\search;

use api\modules\v1\frontend\product\models\Product;
use common\models\ProductQuery;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    const PRODUCT_TYPE_NEW = "new";
    public $keyword;
    public $color;
    public $type;
    public $min_price;
    public $max_price;
    public $installment;
    public $product_option_key;
    public $category_slug;
    public $brand_slug;
    public $ids;
    public $productMetaFilters;

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "unit_price",
            "compare_price",
            "brand" => "brand",
            "category" => "category",
            "additional_data",
            "variants" => "productVariant",
            "additional_data",
            "product_options",
            "tags",
        ];
    }

    public function getProductVariant()
    {
        return $this->productVariants[0] ?? null;
    }

    public function rules(): array
    {
        return [
            [["keyword", "name"], "string"],
            [["installment", "type", "min_price", "max_price", "tags"], "safe"],
            ["product_option_key", "string"],
            [
                ["product_option_key", "category_slug", "brand_slug", "tags", "category_id", "brand_id", "ids"],
                function ($attribute) {
                    $this->$attribute = explode(",", $this->$attribute);
                }
            ]
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find()->allow_sell()->active()
            ->with(["productVariants", "category", "brand"])
            ->groupBy("product.id");
        $this->setProductMetaFilter($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'name',
                    'unit_price',
                    'compare_price',
                    'priority',
                    'created_at',
                ],
            ]
        ]);
        $this->load($params, "");
        if (!$this->validate()) {
            return $dataProvider;
        }
        switch ($this->type) {
            case self::PRODUCT_TYPE_NEW:
                $dataProvider->setSort([
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]);
        }

        if ($this->tags) {
            $tagsRegexp = implode("|", $this->tags);
            $query->andWhere('JSON_EXTRACT(product.tags, "$") REGEXP :tagsRegexp', [':tagsRegexp' => $tagsRegexp]);
        }
        if ($this->min_price) {
            $query->andFilterWhere([">=", "product.unit_price", $this->min_price]);
        }
        if ($this->max_price) {
            $query->andFilterWhere(["<=", "product.unit_price", $this->max_price]);
        }
        if ($this->category_slug) {
            $query->joinWith("category")
                ->andFilterWhere(["category.slug" => $this->category_slug]);
        }
        if ($this->brand_slug) {
            $query->joinWith("brand")
                ->andFilterWhere(["brand.slug" => $this->brand_slug]);
        }
        if ($this->category_id) {
            $query->andFilterWhere(["product.category_id" => $this->category_id]);
        }
        if ($this->brand_id) {
            $query->andFilterWhere(["product.brand_id" => $this->brand_id]);
        }
        if ($this->ids) {
            $query->andFilterWhere(["product.id" => $this->ids]);
        }
        if ($this->product_option_key) {
            $productOptionKeyRegexp = implode("|", $this->product_option_key);
            $query->andWhere('JSON_EXTRACT(product.product_options, "$[*].key") REGEXP :productOptionKeyRegexp', [':productOptionKeyRegexp' => $productOptionKeyRegexp]);
        }
        if ($this->keyword) {
            $query->joinWith(["productVariants", "brand", "category"])
                ->andWhere([
                    "and",
                    [
                        "or",
                        "MATCH (`product`.`name`,`product`.`sku`,`product`.`slug`) AGAINST(:keyword)",
                        "MATCH (`product_variant`.`name`,`product_variant`.`sku`,`product_variant`.`slug`) AGAINST(:keyword)",
                        "MATCH (`category`.`name`,`category`.`code`,`category`.`slug`) AGAINST(:keyword)",
                        "MATCH (`brand`.`name`,`brand`.`code`,`brand`.`slug`) AGAINST(:keyword)"
                    ],
                    [
                        "or",
                        ["like", "product.name", $this->keyword],
                        ["like", "product.sku", $this->keyword],
                        ["like", "product.slug", $this->keyword],
                        ["like", "product_variant.name", $this->keyword],
                        ["like", "product_variant.sku", $this->keyword],
                        ["like", "product_variant.slug", $this->keyword],
                        ["like", "category.name", $this->keyword],
                        ["like", "category.code", $this->keyword],
                        ["like", "category.slug", $this->keyword],
                        ["like", "brand.name", $this->keyword],
                        ["like", "brand.code", $this->keyword],
                        ["like", "brand.slug", $this->keyword],
                    ]
                ])
                ->addParams([":keyword" => $this->keyword]);
        }

        $this->addFilterProductMetaField($query);

        if ($this->installment) {
            $query->joinWith("productMeta")
                ->andFilterWhere(["product_meta.installment" => $this->installment]);
        }
        return $dataProvider;
    }

    public function addFilterProductMetaField(ProductQuery &$query)
    {
        $index = 0;
        $query->joinWith(['productVariants']);
        foreach ($this->productMetaFilters as $key => $productMetaFilter) {
            $index++;
            $jsonSql = "(JSON_EXTRACT(`product_variant`.`meta_field`, \"$[*].key\") REGEXP :key{$index} AND JSON_EXTRACT(`product_variant`.`meta_field`, \"$[*].slug\") REGEXP :productMetaFilter{$index})";
            $query->andWhere($jsonSql, [":key{$index}" => $key, ":productMetaFilter{$index}" => $productMetaFilter]);
        }
    }

    public function setProductMetaFilter($params = [])
    {
        $this->productMetaFilters = [];
        $pattern = "/^filter_+/";
        foreach ($params as $key => $value) {
            if (preg_match($pattern, $key)) {
                $metaFieldKey = preg_replace($pattern, "", $key);
                $this->productMetaFilters[$metaFieldKey] = str_replace(",", "|", $value);
            }
        }
    }
}
