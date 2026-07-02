<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\Department;

class DepartmentSearch extends Department
{
    public $office_name;
    public $user;

    public function formName()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'user_id', 'status'], 'integer'],
            [['office_name', 'user'], 'string'],
            [['name', 'description', 'custom_fields', 'created_at', 'updated_at', 'deleted_at', 'additional_information'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = self::find()
            ->unDelete()
            ->joinWith("office")
            ->joinWith("departmentHead");
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
                "user" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'user'
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
            'department.id' => $this->id,
            'department.office_id' => $this->office_id,
            'department.user_id' => $this->user_id,
            'department.deleted_at' => $this->deleted_at,
            'department.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'department.name', $this->name])
            ->andFilterWhere(['like', 'department.description', $this->description])
            ->andFilterWhere(['like', 'department.custom_fields', $this->custom_fields])
            ->andFilterWhere(['like', 'department.additional_information', $this->additional_information])
            ->andFilterWhere(['like', 'department.created_at', $this->created_at])
            ->andFilterWhere(['like', 'department.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'office.name', $this->office_name])
            ->andFilterWhere(['like', 'user.username', $this->user]);

        return $dataProvider;
    }
}
