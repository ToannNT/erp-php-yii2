<?php

namespace api\modules\v1\admin\inventory\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\inventory\models\InventoryIssue;

/**
 * InventoryIssueSearch represents the model behind the search form about `api\modules\v1\admin\inventory\models\InventoryIssue`.
 */
class InventoryIssueSearch extends InventoryIssue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'inventory_id', 'office_receive_id', 'inventory_receive_id', 'total_number', 'created_by', 'status', 'type', 'order_id'], 'integer'],
            [['code', 'note', 'delivery_date', 'received_date', 'created_at', 'progress_status', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = InventoryIssue::findAssignUser()
            ->joinWith("office")
            ->joinWith("inventory")
            ->joinWith("officeReceive")
            ->joinWith("inventoryReceive")
            ->joinWith("inventoryIssueItems");

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(), [
                'office_receive_name' => [
                    'asc' => ['office.name' => SORT_ASC],
                    'desc' => ['office.name' => SORT_DESC],
                    'label' => 'office_receive_name'
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
            'inventory_issue.id' => $this->id,
            'inventory_issue.office_id' => $this->office_id,
            'inventory_issue.inventory_id' => $this->inventory_id,
            'inventory_issue.office_receive_id' => $this->office_receive_id,
            'inventory_issue.inventory_receive_id' => $this->inventory_receive_id,
            'inventory_issue.total_number' => $this->total_number,
            'inventory_issue.created_by' => $this->created_by,
            'inventory_issue.delivery_date' => $this->delivery_date,
            'inventory_issue.received_date' => $this->received_date,
            'inventory_issue.created_at' => $this->created_at,
            'inventory_issue.updated_at' => $this->updated_at,
            'inventory_issue.deleted_at' => $this->deleted_at,
            'inventory_issue.status' => $this->status,
            'inventory_issue.type' => $this->type,
            'inventory_issue.order_id' => $this->order_id,
        ]);

        $query->andFilterWhere(['like', 'inventory_issue.code', $this->code])
            ->andFilterWhere(['like', 'inventory_issue.note', $this->note]);

        return $dataProvider;
    }
}
