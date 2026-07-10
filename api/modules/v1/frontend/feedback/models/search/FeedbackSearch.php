<?php

namespace api\modules\v1\frontend\feedback\models\search;

use api\modules\v1\frontend\feedback\models\Feedback;
use yii\data\ActiveDataProvider;

class FeedbackSearch extends Feedback
{
    public function rules(): array
    {
        return [
            [['id', 'user_id', 'status'], 'integer'],
            [['title', 'fullname', 'phone', 'email', 'subject'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Feedback::find()->where(['status' => Feedback::STATUS_ACTIVE]);

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
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
