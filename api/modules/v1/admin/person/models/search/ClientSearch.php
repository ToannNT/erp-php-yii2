<?php

namespace api\modules\v1\admin\person\models\search;

use api\modules\v1\admin\person\models\Client;
use yii\data\ActiveDataProvider;

class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'unique_external_id', 'owner_id', 'status'], 'integer'],
            [['name', 'description', 'note', 'phone', 'biz_phone', 'email', 'website', 'type', 'postal_code', 'time_zone', 'address_1', 'address_2', 'avatar', 'custom_fields', 'additional_information', 'created_at', 'updated_at', 'deleted_at', 'renewal_date', 'groups', 'currency', 'language', 'code'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = self::find()->active();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(), []),
            "defaultOrder" => [
                "id" => SORT_DESC
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'unique_external_id' => $this->unique_external_id,
            'owner_id' => $this->owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'renewal_date' => $this->renewal_date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'biz_phone', $this->biz_phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'postal_code', $this->postal_code])
            ->andFilterWhere(['like', 'time_zone', $this->time_zone])
            ->andFilterWhere(['like', 'address_1', $this->address_1])
            ->andFilterWhere(['like', 'address_2', $this->address_2])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'custom_fields', $this->custom_fields])
            ->andFilterWhere(['like', 'additional_information', $this->additional_information])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }

}