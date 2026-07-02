<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\Inventory;

class InventorySearch extends Inventory
{
    public $owner;
    public $office_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'priority', 'parent_id', 'owner_id', 'status'], 'integer'],
            [['name', 'type', 'code', 'description', 'group_id', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['owner', 'office_name'], 'string']
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
        $query = Inventory::find()
            ->unDelete()
            ->joinWith("office")
            ->joinWith("owner");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ]
        ]);

        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "owner" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'owner'
                ],
                "office_name" => [
                    'asc' => ['office.name' => SORT_ASC],
                    'desc' => ['office.name' => SORT_DESC],
                    'label' => 'office_name'
                ],
            ]),
            "defaultOrder" => [
                "id" => SORT_DESC
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
            ->andFilterWhere(['like', 'inventory.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'user.username', $this->owner])
            ->andFilterWhere(['like', 'office.name', $this->office_name]);

        return $dataProvider;
    }
}
