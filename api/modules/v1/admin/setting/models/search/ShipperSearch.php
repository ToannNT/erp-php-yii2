<?php

namespace api\modules\v1\admin\setting\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\Shipper;

/**
 * ShipperSearch represents the model behind the search form about `api\modules\v1\admin\setting\models\Shipper`.
 */
class ShipperSearch extends Shipper
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'created_by'], 'integer'],
            [['name', 'short_name', 'slug', 'thumbnail', 'token', 'service_extras', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Shipper::find()->notDelete();

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
            'type' => $this->type,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_name', $this->short_name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'thumbnail', $this->thumbnail])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'service_extras', $this->service_extras]);

        return $dataProvider;
    }
}
