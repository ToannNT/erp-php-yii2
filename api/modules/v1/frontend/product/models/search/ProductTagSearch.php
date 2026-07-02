<?php

namespace api\modules\v1\frontend\product\models\search;

use api\modules\v1\frontend\product\models\Product;
use yii\data\ActiveDataProvider;
use common\models\Tag;

class ProductTagSearch extends Tag
{
    public function rules(): array
    {
        return [
            [['id', 'popularity'], 'integer'],
            [['name', 'slug', 'type', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Tag::find();
        $limit = isset($params['limit']) ? (int)$params['limit'] : null;
        if ($limit !== null) {
            $query->limit($limit);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params, "");
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'popularity' => $this->popularity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'type', $this->type]);

        $query->andWhere(['type' => Product::TYPE_PRODUCT])->orderBy(['popularity' => SORT_DESC]);
        return $dataProvider;
    }

}