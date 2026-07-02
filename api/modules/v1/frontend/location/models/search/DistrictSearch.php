<?php

namespace api\modules\v1\frontend\location\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\frontend\location\models\District;

/**
 * DistrictSearch represents the model behind the search form of `api\modules\v1\frontend\location\models\District`.
 */
class DistrictSearch extends District
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'name_en', 'full_name', 'full_name_en', 'code_name', 'province_code'], 'safe'],
            [['administrative_unit_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search(array $params)
    {
        $query = District::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 1000,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'administrative_unit_id' => $this->administrative_unit_id,
            'province_code' => $this->province_code
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'full_name_en', $this->full_name_en])
            ->andFilterWhere(['like', 'code_name', $this->code_name]);

        return $dataProvider;
    }
}
