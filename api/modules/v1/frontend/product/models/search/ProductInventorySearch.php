<?php

namespace api\modules\v1\frontend\product\models\search;

use common\models\ProductInventory;
use yii\data\ActiveDataProvider;

class ProductInventorySearch extends ProductInventory
{
    public function fields(): array
    {
        return [
            "office_name" => function () {
                return $this->office->name;
            },
            "latitude" => function () {
                return $this->office->latitude;
            },
            "longitude" => function () {
                return $this->office->longitude;
            },
            "address" => function () {
                return $this->office->address1;
            },
        ];
    }

    public function rules(): array
    {
        return [
            [["product_variant_id", "product_id"], "integer"]
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find()->joinWith("offices")->groupBy("office_id");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params
            ],
            'sort' => [
                'params' => $params
            ]
        ]);
        $this->load($params, "");
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "product_variant_id" => $this->product_variant_id,
            "product_id" => $this->product_id
        ]);
        return $dataProvider;
    }
}
