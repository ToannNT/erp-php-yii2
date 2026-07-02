<?php

namespace api\modules\v1\admin\general\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\general\models\Office;

/**
 * OfficeSearch represents the model behind the search form about `api\modules\v1\admin\general\models\Office`.
 */
class OfficeSearch extends Office
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'contact_person_id'], 'integer'],
            [['type', 'name', 'description', 'custom_fields', 'domains', 'note', 'postal_code', 'health_score', 'account_tier', 'renewal_date', 'industry', 'work_phone', 'address1', 'address2', 'state', 'city', 'country', 'created_at', 'updated_at', 'deleted_at', 'email', 'street', 'biz_phone', 'security_code', 'additional_information'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
        $query = Office::withRole();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'status' => $this->status,
            'contact_person_id' => $this->contact_person_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'custom_fields', $this->custom_fields])
            ->andFilterWhere(['like', 'domains', $this->domains])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'postal_code', $this->postal_code])
            ->andFilterWhere(['like', 'health_score', $this->health_score])
            ->andFilterWhere(['like', 'account_tier', $this->account_tier])
            ->andFilterWhere(['like', 'renewal_date', $this->renewal_date])
            ->andFilterWhere(['like', 'industry', $this->industry])
            ->andFilterWhere(['like', 'work_phone', $this->work_phone])
            ->andFilterWhere(['like', 'address1', $this->address1])
            ->andFilterWhere(['like', 'address2', $this->address2]);

        return $dataProvider;
    }
}
