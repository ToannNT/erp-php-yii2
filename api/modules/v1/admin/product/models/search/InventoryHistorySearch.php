<?php

namespace api\modules\v1\admin\product\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\InventoryHistory;

/**
 * InventoryHistorySearch represents the model behind the search form about `api\modules\v1\admin\product\models\InventoryHistory`.
 */
class InventoryHistorySearch extends InventoryHistory
{
    public $product_variant_name;
    public $inventory_name;
    public $created_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'inventory', 'office_id', 'inventory_id', 'product_id', 'product_variant_id', 'status', 'type'], 'integer'],
            [['action', 'change_quantity', 'voucher_code', 'created_at', 'updated_at', 'link_detail', 'product_variant_name', 'inventory_name', 'created_by'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = InventoryHistory::find()
            ->joinWith("productVariant")
            ->joinWith("createdBy")
            ->joinWith("modelInventory");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "inventory_name" => [
                    'asc' => ['inventory.name' => SORT_ASC],
                    'desc' => ['inventory.name' => SORT_DESC],
                    'label' => 'inventory_name'
                ],
                "product_variant_name" => [
                    'asc' => ['product_variant.name' => SORT_ASC],
                    'desc' => ['product_variant.name' => SORT_DESC],
                    'label' => 'product_variant_name'
                ],
                "created_by" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'created_by'
                ],
            ]),
            "defaultOrder" => [
                "id" => SORT_DESC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'inventory_history.id' => $this->id,
            'inventory_history.inventory' => $this->inventory,
            'inventory_history.office_id' => $this->office_id,
            'inventory_history.inventory_id' => $this->inventory_id,
            'inventory_history.product_id' => $this->product_id,
            'inventory_history.product_variant_id' => $this->product_variant_id,
            'inventory_history.created_at' => $this->created_at,
            'inventory_history.updated_at' => $this->updated_at,
            'inventory_history.status' => $this->status,
            'inventory_history.type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'change_quantity', $this->change_quantity])
            ->andFilterWhere(['like', 'voucher_code', $this->voucher_code])
            ->andFilterWhere(['like', 'link_detail', $this->link_detail])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name])
            ->andFilterWhere(['like', 'product_variant.name', $this->product_variant_name])
            ->andFilterWhere(['like', 'user.username', $this->created_by]);

        return $dataProvider;
    }
}
