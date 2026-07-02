<?php

namespace api\modules\v1\admin\inventory\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\inventory\models\InventoryHistory;

/**
 * InventoryHistorySearch represents the model behind the search form about `api\modules\v1\admin\inventory\models\InventoryHistory`.
 */
class InventoryHistorySearch extends InventoryHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'inventory', 'office_id', 'inventory_id', 'product_id', 'product_variant_id', 'status', 'type'], 'integer'],
            [['action', 'change_quantity', 'voucher_code', 'created_at', 'updated_at', 'link_detail', 'created_by'], 'safe'],
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
            ->joinWith("createdBy")
            ->joinWith("modelOffice")
            ->joinWith("productVariant");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(), [
                'created_by' => [
                    'asc' => ['user.id' => SORT_ASC],
                    'desc' => ['user.id' => SORT_DESC],
                    'label' => 'created_by'
                ],
                'product_variant_name' => [
                    'asc' => ['product_variant.name' => SORT_ASC],
                    'desc' => ['product_variant.name' => SORT_DESC],
                    'label' => 'product_variant_name'
                ]
            ]),
            'defaultOrder' => [
                'id' => SORT_DESC
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
            'inventory' => $this->inventory,
            'office_id' => $this->office_id,
            'inventory_history.inventory_id' => $this->inventory_id,
            'inventory_history.product_id' => $this->product_id,
            'inventory_history.product_variant_id' => $this->product_variant_id,
            'inventory_history.status' => $this->status,
            'inventory_history.type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'inventory_history.action', $this->action])
            ->andFilterWhere(['like', 'inventory_history.change_quantity', $this->change_quantity])
            ->andFilterWhere(['like', 'inventory_history.voucher_code', $this->voucher_code])
            ->andFilterWhere(['like', 'inventory_history.link_detail', $this->link_detail])
            ->andFilterWhere(['like', 'user.username', $this->created_by])
            ->andFilterWhere(['like', 'inventory_history.created_at', $this->created_at])
            ->andFilterWhere(['like', 'inventory_history.updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
