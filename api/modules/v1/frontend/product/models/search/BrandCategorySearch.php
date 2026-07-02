<?php

namespace api\modules\v1\frontend\product\models\search;

use yii\data\ActiveDataProvider;

class BrandCategorySearch extends \common\models\CategoryBrand
{
    public $category_name;
    public $brand_name;
    public $category_slug;
    public $brand_slug;

    public function fields()
    {
        return array_merge(parent::fields(), [
            "category" => "category",
            "brand" => "brand"
        ]);
    }

    public function rules()
    {
        return [
            [["category_name", "brand_name", "brand_slug"], "string"],
            [["id"], "safe"]
        ];
    }

    public function search($params)
    {
        $query = self::find()
            ->joinWith("brand")
            ->joinWith("category");

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
            "id" => $this->id,
            "brand.name" => $this->brand_name,
            "brand.slug" => $this->brand_slug,
            "category.name" => $this->category_name,
            "category.slug" => $this->category_slug
        ]);
        return $dataProvider;
    }
}