<?php

namespace api\modules\v1\admin\setting\models\search;

use api\modules\v1\admin\setting\models\Office;
use yii\data\ActiveDataProvider;

class OfficeSearch extends Office
{
    public $contact_name;

    public function rules()
    {
        return [
            [['id', 'status', 'contact_person_id'], 'integer'],
            [['type', 'name', 'description', 'custom_fields', 'domains', 'note', 'postal_code', 'health_score', 'account_tier', 'renewal_date', 'industry', 'work_phone', 'address1', 'address2', 'created_at', 'updated_at', 'deleted_at', 'email', 'biz_phone', 'security_code', 'additional_information'], 'safe'],
            [['latitude', 'longitude'], 'number'],
        ];
    }

    public function search($params)
    {
        $query = self::find()->unDelete()->joinWith("contactPerson");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ]
        ]);
        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "contact_name" => [
                    'asc' => ['contact.name' => SORT_ASC],
                    'desc' => ['contact.name' => SORT_DESC],
                    'label' => 'contact_name'
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
            'office.id' => $this->id,
            'office.deleted_at' => $this->deleted_at,
            'office.status' => $this->status,
            'office.contact_person_id' => $this->contact_person_id,
        ]);

        $query->andFilterWhere(['like', 'office.type', $this->type])
            ->andFilterWhere(['like', 'office.name', $this->name])
            ->andFilterWhere(['like', 'office.description', $this->description])
            ->andFilterWhere(['like', 'office.custom_fields', $this->custom_fields])
            ->andFilterWhere(['like', 'office.domains', $this->domains])
            ->andFilterWhere(['like', 'office.note', $this->note])
            ->andFilterWhere(['like', 'office.postal_code', $this->postal_code])
            ->andFilterWhere(['like', 'office.health_score', $this->health_score])
            ->andFilterWhere(['like', 'office.account_tier', $this->account_tier])
            ->andFilterWhere(['like', 'office.renewal_date', $this->renewal_date])
            ->andFilterWhere(['like', 'office.industry', $this->industry])
            ->andFilterWhere(['like', 'office.work_phone', $this->work_phone])
            ->andFilterWhere(['like', 'office.address1', $this->address1])
            ->andFilterWhere(['like', 'office.address2', $this->address2])
            ->andFilterWhere(['like', 'office.email', $this->email])
            ->andFilterWhere(['like', 'office.biz_phone', $this->biz_phone])
            ->andFilterWhere(['like', 'office.security_code', $this->security_code])
            ->andFilterWhere(['like', 'office.additional_information', $this->additional_information])
            ->andFilterWhere(['like', 'office.created_at', $this->created_at])
            ->andFilterWhere(['like', 'office.updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
