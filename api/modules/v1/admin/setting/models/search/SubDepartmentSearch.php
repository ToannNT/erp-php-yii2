<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\SubDepartment;

class SubDepartmentSearch extends SubDepartment
{
    public $user;
    public $department_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'department_id', 'user_id', 'status'], 'integer'],
            [['name', 'description', 'custom_fields', 'user', 'department_name', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = SubDepartment::find()
            ->unDelete()
            ->joinWith("department")
            ->joinWith("subDepartmentHead");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ]
        ]);
        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "user" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'user'
                ],
                "department_name" => [
                    'asc' => ['department.name' => SORT_ASC],
                    'desc' => ['department.name' => SORT_DESC],
                    'label' => 'department_name'
                ]
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
            'sub_department.id' => $this->id,
            'sub_department.department_id' => $this->department_id,
            'sub_department.user_id' => $this->user_id,
            'sub_department.deleted_at' => $this->deleted_at,
            'sub_department.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'sub_department.name', $this->name])
            ->andFilterWhere(['like', 'sub_department.description', $this->description])
            ->andFilterWhere(['like', 'sub_department.custom_fields', $this->custom_fields])
            ->andFilterWhere(['like', 'sub_department.created_at', $this->created_at])
            ->andFilterWhere(['like', 'sub_department.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'user.username', $this->user])
            ->andFilterWhere(['like', 'department.name', $this->department_name]);

        return $dataProvider;
    }
}
