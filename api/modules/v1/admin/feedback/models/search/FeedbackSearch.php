<?php

namespace api\modules\v1\admin\feedback\models\search;

use api\modules\v1\admin\feedback\models\Feedback;
use yii\data\ActiveDataProvider;

class FeedbackSearch extends Feedback
{
    public function rules(): array
    {
        return [
            [['id', 'user_id', 'status', 'is_confirm_term'], 'integer'],
            [['subject', 'title', 'fullname', 'phone', 'email', 'content', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Feedback::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['params' => $params],
            'sort' => [
                'params' => $params,
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'is_confirm_term' => $this->is_confirm_term,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'subject', $this->subject]);

        return $dataProvider;
    }
}
