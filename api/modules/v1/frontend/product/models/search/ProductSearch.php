<?php

namespace api\modules\v1\frontend\product\models\search;

use api\modules\v1\frontend\product\models\Product;
use api\modules\v1\frontend\product\models\ProductVariant;
use common\models\ProductQuery;
use yii\data\ActiveDataProvider;
use function foo\func;

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
    public $productMetaFilters;

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "unit_price",
            "brand" => "brand",
            "category" => "category",
            "additional_data",
            "variants" => "productVariant",
            "additional_data",
            "product_options",
            "tags",
        ];
    }

    public function getProductVariant($selects = [])
    {
        return $this->hasMany(ProductVariant::class, ["product_id" => "id"])->limit(1);
    }

    public function rules(): array
    {
        return [
            ["keyword", "string"],
            [["installment", "type", "min_price", "max_price", "tags"], "safe"],
            ["product_option_key", "string"],
            [
                ["product_option_key", "category_slug", "brand_slug", "tags"],
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
                'params' => $params
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
        if ($this->keyword) {
            $query->joinWith(["productVariants", "brand", "category"])
                ->andWhere([
                    "or",
                    "MATCH (`product`.`name`,`product`.`sku`,`product`.`slug`) AGAINST(:keyword)",
                    "MATCH (`product_variant`.`name`,`product_variant`.`sku`,`product_variant`.`slug`) AGAINST(:keyword)",
                    "MATCH (`category`.`name`,`category`.`code`,`category`.`slug`) AGAINST(:keyword)",
                    "MATCH (`brand`.`name`,`brand`.`code`,`brand`.`slug`) AGAINST(:keyword)"
                ])
                ->addParams([":keyword" => $this->keyword]);
        }

        $this->addFilterProductMetaField($query);

        $query->andFilterWhere([
            "product.installment" => $this->installment
        ]);
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
