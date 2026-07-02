<?php

namespace api\modules\v1\frontend\comment\models\search;

use api\modules\v1\frontend\comment\models\Comment;
use yii\data\ActiveDataProvider;

class CommentSearch extends Comment
{

    public function rules(): array
    {
        return [
            ["content", "string"],
            [["status"], "integer"],
            [["created_at", "content", "updated_at", "product_variant_id"], "safe"],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find()->active();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params
            ],
            'sort' => [
                'params' => $params
            ]
        ]);
        $this->load($params, "");
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'user_id' => $this->user_id,
            "type" => $this->type,
            "module_id" => $this->module_id
        ]);
        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }
        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }
        return $dataProvider;
    }

    public function searchFind($params)
    {
        $query = self::find()->active();
        $this->load($params, "");
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'user_id' => $this->user_id,
            "type" => $this->type,
            "module_id" => $this->module_id
        ]);
        return $query->one();
    }
}
