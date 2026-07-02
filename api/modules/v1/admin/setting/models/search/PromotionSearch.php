<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\Promotion;

class PromotionSearch extends Promotion
{

    public function rules()
    {
        return [
            [["discount_value"], "number"],
            [["id", "discount_type", "limit", "order_total_required", "status", "promotion_type"], "integer"],
            [["code", "title"], "string"],
            [["start_date", "end_date", "created_at", "updated_at"], "safe"]
        ];
    }

    public function search($params)
    {
        $query = Promotion::find()->unDelete();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params
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
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'limit' => $this->limit,
            'order_total_required' => $this->order_total_required,
            'group_customer' => $this->group_customers,
            'status' => $this->status,
            'condition_type' => $this->condition_type,
            'promotion_type' => $this->promotion_type,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'start_date', $this->start_date])
            ->andFilterWhere(['like', 'end_date', $this->end_date]);
        return $dataProvider;
    }
}
