<?php

namespace api\modules\v1\admin\product\models\search;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\Product;

class ProductSearch extends Product
{
    public $category_name;
    public $brand_name;
    public $supplier_name;
    public $tag;
    public $product_name;

    public function rules()
    {
        return [
            [["name", "product_name", "sku", "category_name", "brand_name", "supplier_name", "description", "short_description", "dimension", "tag"], "string"],
            [["id", "status", "allow_sell"], "integer"],
            [["created_at", "updated_at"], "string"]
        ];
    }

    public function search($params)
    {
        $query = self::find()->unDelete()
            ->joinWith("category")
            ->joinWith("brand")
            ->joinWith("suppliers")
            ->joinWith("productVariants")
            ->groupBy("product.id");
        if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $query->andWhere(["in", "supplier_id", array_column(Yii::$app->user->identity->suppliers, "id")]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'product.id' => $this->id,
            'product.status' => $this->status,
            'allow_sell' => $this->allow_sell,
        ]);

        $query->andFilterWhere([
            "or",
            ["like", "product.name", $this->product_name],
            ["like", "product_variant.name", $this->product_name],
        ])->andFilterWhere([
            "or",
            ["like", "product.sku", $this->sku],
            ["like", "product_variant.sku", $this->sku],
        ])->andFilterWhere([
            "or",
            ["like", "product.bar_code", $this->bar_code],
            ["like", "product_variant.barcode", $this->bar_code],
        ])
            ->andFilterWhere(['like', 'product.description', $this->description])
            ->andFilterWhere(['like', 'product.short_description', $this->short_description])
            ->andFilterWhere(['like', 'product.created_at', $this->created_at])
            ->andFilterWhere(['like', 'product.updated_at', $this->updated_at])
            ->andFilterWhere(["like", "category.name", $this->category_name])
            ->andFilterWhere(["like", "brand.name", $this->brand_name])
            ->andFilterWhere(["like", "supplier.name", $this->supplier_name]);
        return $dataProvider;
    }
}
