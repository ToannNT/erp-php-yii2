<?php

namespace api\modules\v1\admin\product\models\search;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\ProductVariant;

class ProductVariantSearch extends ProductVariant
{

    public $supplier_name;
    public $brand_id;

    public function rules()
    {
        return [
            [["name", "barcode", "slug", "sku", "dimension", "supplier_name"], "string"],
            [["id", "status", "product_id", "brand_id"], "integer"],
            [["created_at", "updated_at"], "safe"]
        ];
    }

    public function search($params)
    {
        $query = self::find()
            ->unDelete()
            ->joinWith("productInventories")
            // add join product => product_supplier => supplier
            ->joinWith("product", true, "JOIN")
            ->groupBy("product_variant.id");
        if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
//            $query->joinWith("product");
            $suppliers = Yii::$app->user->identity->suppliers;
            $query->andWhere(["in", "supplier_id", array_map(function ($suppliers) {
                return $suppliers->id;
            }, $suppliers)]);
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
            "product_variant.product_id" => $this->product_id,
            'product_variant.id' => $this->id,
            'product_variant.status' => $this->status,
            'product_variant.deleted_at' => $this->deleted_at,
            'product_variant.slug' => $this->slug,
            'product.brand_id' => $this->brand_id
        ]);

        $query->andFilterWhere(['like', 'product_variant.name', $this->name])
            ->andFilterWhere(['like', 'product_variant.created_at', $this->created_at])
            ->andFilterWhere(['like', 'product_variant.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'supplier.name', $this->supplier_name]);
        return $dataProvider;
    }
}
