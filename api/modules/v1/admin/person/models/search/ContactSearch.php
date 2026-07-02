<?php

namespace api\modules\v1\admin\person\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\person\models\Contact;

class ContactSearch extends Contact
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'unique_external_id', 'status'], 'integer'],
            [['first_name', 'last_name', 'name', 'description', 'note', 'phone', 'email', 'type', 'postal_code', 'time_zone', 'address_1', 'address_2', 'country', 'state', 'city', 'avatar', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = self::find()->unDelete();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'unique_external_id' => $this->unique_external_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'postal_code', $this->postal_code])
            ->andFilterWhere(['like', 'time_zone', $this->time_zone])
            ->andFilterWhere(['like', 'address_1', $this->address_1])
            ->andFilterWhere(['like', 'address_2', $this->address_2])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'avatar', $this->avatar]);
        return $dataProvider;
    }
}
