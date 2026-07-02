<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\OfficePolicy;

class OfficePolicySearch extends OfficePolicy
{

    public $office_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['office_id', 'name', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['office_name', 'string']
        ];
    }


    public function search($params)
    {
        $query = OfficePolicy::find()->joinWith("office")->unDelete();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ]
        ]);
        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
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
            'office_policy.id' => $this->id,
            'office_policy.deleted_at' => $this->deleted_at,
            'office_policy.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'office_policy.office_id', $this->office_id])
            ->andFilterWhere(['like', 'office_policy.name', $this->name])
            ->andFilterWhere(['like', 'office_policy.description', $this->description])
            ->andFilterWhere(['like', 'office_policy.created_at', $this->created_at])
            ->andFilterWhere(['like', 'office_policy.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'office.name', $this->office_name]);
        return $dataProvider;
    }
}
