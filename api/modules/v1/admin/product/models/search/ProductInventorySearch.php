<?php

namespace api\modules\v1\admin\product\models\search;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\ProductInventory;

class ProductInventorySearch extends ProductInventory
{
    public $inventory_name;
    public $product_variant_name;
    public $product_variant_sku;
    public $supplier_name;
    public $type_available;

    public function rules()
    {
        return [
            [["inventory_id", "product_id", "product_variant_id", "id"], "integer"],
            [["inventory_name", "product_variant_name", "product_variant_sku", "created_at", "updated_at", "supplier_name"], "string"],
            ["type_available", "safe"]
        ];
    }

    public function fields()
    {
        return parent::fields();
    }

    public function search($params)
    {
        $query = self::find()
            ->joinWith("product", true, "JOIN")
            ->joinWith("productVariant", true, "JOIN")
//            ->joinWith("inventory", true, "JOIN")
            ->joinWith("office", true, "JOIN")
            ->groupBy("product_inventory.id");

        if (Yii::$app->user->can(User::ROLE_MANAGER)) {
            $query->andWhere(["product_inventory.inventory_id" => array_column(Yii::$app->user->identity->inventorys, "id")]);
        } else if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $query->andWhere(["supplier_id" => array_column(Yii::$app->user->identity->suppliers, "id")]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ]
        ]);

        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "product_variant_sku" => [
                    'asc' => ['product_variant.sku' => SORT_ASC],
                    'desc' => ['product_variant.sku' => SORT_DESC],
                    'label' => 'product_variant_sku'
                ],
                "product_variant_name" => [
                    'asc' => ['product_variant.name' => SORT_ASC],
                    'desc' => ['product_variant.name' => SORT_DESC],
                    'label' => 'product_variant_name'
                ],
                "inventory_name" => [
                    'asc' => ['inventory.name' => SORT_ASC],
                    'desc' => ['inventory.name' => SORT_DESC],
                    'label' => 'inventory_name'
                ],
            ]),
            'defaultOrder' => [
                'inventory_id' => SORT_DESC
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->type_available) {
            $query->andWhere([">", "available", 0]);
        }
        $query->andFilterWhere([
            'product_inventory.id' => $this->id,
            'product_inventory.inventory_id' => $this->inventory_id,
            'product_inventory.product_id' => $this->product_id,
            'product_inventory.product_variant_id' => $this->product_variant_id,
        ]);

        $query
            ->andFilterWhere(['like', 'product_variant.sku', $this->product_variant_sku])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name])
            ->andFilterWhere(['like', 'product_inventory.created_at', $this->created_at])
            ->andFilterWhere(['like', 'product_inventory.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'product_variant.name', $this->product_variant_name])
            ->andFilterWhere(['like', 'supplier.name', $this->supplier_name]);
        return $dataProvider;
    }
}
