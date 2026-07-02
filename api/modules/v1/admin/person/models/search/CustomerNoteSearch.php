<?php

namespace api\modules\v1\admin\person\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\person\models\CustomerNote;

/**
 * CustomerNoteSearch represents the model behind the search form about `api\modules\v1\admin\person\models\CustomerNote`.
 */
class CustomerNoteSearch extends CustomerNote
{
    public $customer_name;
    public $created_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'group', 'priority', 'status'], 'integer'],
            [['note', 'created_at', 'updated_at', 'deleted_at', 'customer_name', 'created_by'], 'safe'],
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
        $query = CustomerNote::find()
            ->joinWith("customer")
            ->joinWith("createdBy");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "created_by" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'created_by'
                ],
                "customer_name" => [
                    'asc' => ['customer.name' => SORT_ASC],
                    'desc' => ['customer.name' => SORT_DESC],
                    'label' => 'customer_name'
                ]
            ]),
            "defaultOrder" => [
                "id" => SORT_DESC
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'customer_note.id' => $this->id,
            'customer_note.customer_id' => $this->customer_id,
            'customer_note.group' => $this->group,
            'customer_note.priority' => $this->priority,
            'customer_note.deleted_at' => $this->deleted_at,
            'customer_note.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'customer_note.note', $this->note])
            ->andFilterWhere(['like', 'user.username', $this->created_by])
            ->andFilterWhere(['like', 'customer.name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_note.created_at', $this->created_at])
            ->andFilterWhere(['like', 'customer_note.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'customer.name', $this->customer_name]);

        return $dataProvider;
    }
}
