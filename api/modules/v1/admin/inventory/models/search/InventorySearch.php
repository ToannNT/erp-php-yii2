<?php

namespace api\modules\v1\admin\inventory\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\inventory\models\Inventory;
use yii\db\Query;

class InventorySearch extends Inventory
{

    public $product_variant_name;
    public $product_variant_sku;
    public $product_variant_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'priority', 'parent_id', 'owner_id', 'status', 'product_variant_id'], 'integer'],
            [['name', 'product_variant_name', 'product_variant_sku', 'type', 'code', 'description', 'group_id', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Inventory::find()->active()
            ->joinWith(["productInventories" => function (Query $query) {
                $query->andFilterWhere([
                    'product_variant.id' => $this->product_variant_id,
                ]);
                $query->andFilterWhere(['like', 'product_variant.name', $this->product_variant_name])
                    ->andFilterWhere(['like', 'product_variant.sku', $this->product_variant_sku]);
            }], true, "JOIN");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ]
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'product_variant_name' => [
                    'asc' => ['product_variant.name' => SORT_ASC],
                    'desc' => ['product_variant.name' => SORT_DESC],
                    'label' => 'product_variant_name'
                ],
                'product_variant_sku' => [
                    'asc' => ['product_variant.sku' => SORT_ASC],
                    'desc' => ['product_variant.sku' => SORT_DESC],
                    'label' => 'product_variant_sku'
                ],
                'available' => [
                    'asc' => ['product_inventory.available' => SORT_ASC],
                    'desc' => ['product_inventory.available' => SORT_DESC],
                    'label' => 'available'
                ],
                'quantity' => [
                    'asc' => ['product_inventory.quantity' => SORT_ASC],
                    'desc' => ['product_inventory.quantity' => SORT_DESC],
                    'label' => 'quantity'
                ],
                'incoming' => [
                    'asc' => ['product_inventory.incoming' => SORT_ASC],
                    'desc' => ['product_inventory.incoming' => SORT_DESC],
                    'label' => 'incoming'
                ],
                'on_way' => [
                    'asc' => ['product_inventory.on_way' => SORT_ASC],
                    'desc' => ['product_inventory.on_way' => SORT_DESC],
                    'label' => 'on_way'
                ],
                'committed' => [
                    'asc' => ['product_inventory.committed' => SORT_ASC],
                    'desc' => ['product_inventory.committed' => SORT_DESC],
                    'label' => 'committed'
                ],
                'unit_price' => [
                    'asc' => ['product_inventory.unit_price' => SORT_ASC],
                    'desc' => ['product_inventory.unit_price' => SORT_DESC],
                    'label' => 'unit_price'
                ],
                'sll_price' => [
                    'asc' => ['product_inventory.sll_price' => SORT_ASC],
                    'desc' => ['product_inventory.sll_price' => SORT_DESC],
                    'label' => 'sll_price'
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'inventory.id' => $this->id,
            'inventory.office_id' => $this->office_id,
            'inventory.priority' => $this->priority,
            'inventory.parent_id' => $this->parent_id,
            'inventory.owner_id' => $this->owner_id,
            'inventory.deleted_at' => $this->deleted_at,
            'inventory.status' => $this->status,
        ]);
        $query->andFilterWhere(['like', 'inventory.name', $this->name])
            ->andFilterWhere(['like', 'inventory.type', $this->type])
            ->andFilterWhere(['like', 'inventory.code', $this->code])
            ->andFilterWhere(['like', 'inventory.description', $this->description])
            ->andFilterWhere(['like', 'inventory.group_id', $this->group_id])
            ->andFilterWhere(['like', 'inventory.created_at', $this->created_at])
            ->andFilterWhere(['like', 'inventory.updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
