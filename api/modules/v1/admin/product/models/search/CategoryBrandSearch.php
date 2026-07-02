<?php

namespace api\modules\v1\admin\product\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\CategoryBrand;

/**
 * CategoryBrandSearch represents the model behind the search form of `api\modules\v1\admin\product\models\CategoryBrand`.
 */
class CategoryBrandSearch extends CategoryBrand
{
    public $category_name;
    public $brand_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'category_id', 'status'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at', 'category_name', 'brand_name'], 'safe'],
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
    public function search($params)
    {
        $query = CategoryBrand::find()->joinWith(["brand", "category"]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ])->andFilterWhere(["LIKE", "category.name", $this->category_name])
            ->andFilterWhere(["LIKE", "brand.name", $this->brand_name]);

        return $dataProvider;
    }
}
