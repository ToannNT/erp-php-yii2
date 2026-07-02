<?php

namespace api\modules\v1\frontend\pos\models\search;

use common\models\Product;
use yii\data\ActiveDataProvider;
use common\models\ProductVariant;
use common\models\ProductInventory;
use Yii;

class ProductVariantSearch extends ProductVariant
{
    public $q;
    public $category_id;

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "product_id",
            "sku",
            "barcode",
            "unit_price",
            "sll_price",
            "images" => function () {
                return json_decode($this->images);
            },
            "available" => "available"
        ];
    }

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'sku', 'barcode', 'code', 'slug', 'q', 'category_id'], 'string'],
        ];
    }

    public function getAvailable()
    {
        $inventory = Yii::$app->user->identity->inventoryFirst;
        return $this->hasMany(ProductInventory::class, ['product_variant_id' => 'id'])
//            ->andWhere(["inventory_id" => array_column(Yii::$app->user->identity->inventorys, "id")])
            ->andWhere(["inventory_id" => $inventory->id])
            ->sum('available');
    }

    public function getQuery()
    {
        $query = self::find()
            ->joinWith("product")
            ->joinWith("inventorys")
            ->andWhere(["allow_sell" => Product::STATUS_ALLOW_SELL]);
        if (Yii::$app->user->can("seller")) {
            $query->andWhere(["office_id" => array_column(Yii::$app->user->identity->offices, "id")]);
        }
        $query->groupBy("product_variant.id");
        return $query;
    }

    public function search($params): ActiveDataProvider
    {
        // add allow sell
        $query = $this->getQuery();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
            ],
        ]);
        if (!($this->load($params, "") && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "product_variant.id" => $this->id,
            "product_variant.slug" => $this->slug,
            "product_variant.barcode" => $this->barcode,
            "product_variant.code" => $this->code,
            "product.category_id" => $this->category_id,
        ]);
        $query->andFilterWhere([
            "or",
            ["like", "product_variant.name", $this->q],
            ["product_variant.sku" => $this->q],
            ["product_variant.barcode" => $this->q]
        ]);
        return $dataProvider;
    }
}
