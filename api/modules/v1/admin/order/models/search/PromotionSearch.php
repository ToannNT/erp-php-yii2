<?php

namespace api\modules\v1\admin\order\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\order\models\Promotion;

/**
 * PromotionSearch represents the model behind the search form about `api\modules\v1\admin\order\models\Promotion`.
 */
class PromotionSearch extends Promotion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'discount_type', 'limit', 'used', 'status', 'promotion_type'], 'integer'],
            [['title', 'code', 'description', 'start_date', 'end_date', 'group_customers', 'office_ids', 'condition_items', 'condition_type', 'created_at', 'updated_at', 'deleted_at', 'offices'], 'safe'],
            [['discount_value', 'order_total_required'], 'number'],
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
        $query = Promotion::find()->active()->available();

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
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'limit' => $this->limit,
            'used' => $this->used,
            'order_total_required' => $this->order_total_required,
            'status' => $this->status,
            'promotion_type' => $this->promotion_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'group_customers', $this->group_customers])
            ->andFilterWhere(['like', 'office_ids', $this->office_ids])
            ->andFilterWhere(['like', 'condition_items', $this->condition_items])
            ->andFilterWhere(['like', 'condition_type', $this->condition_type])
            ->andFilterWhere(['like', 'deleted_at', $this->deleted_at])
            ->andFilterWhere(['like', 'offices', $this->offices]);

        return $dataProvider;
    }
}
