<?php

namespace api\modules\v1\frontend\article\models\search;


use api\modules\v1\frontend\article\models\Article;
use yii\data\ActiveDataProvider;

class ArticleSearch extends Article
{
    public function fields()
    {
        return [
            "id",
            "slug",
            "title",
            "category_id",
            "description" => function () {
                return $this->view;
            },
            "thumbnail_base_url",
            "thumbnail_path",
            "created_by" => "authorName",
            "category" => "category",
            "created_at" => function () {
                return date("Y-m-d", $this->created_at);
            },
            "updated_at" => function () {
                return date("Y-m-d", $this->updated_at);
            },
            "published_at" => function () {
                return date("Y-m-d", $this->published_at);
            },
            "category_slug" => "categorySlug",
        ];
    }

    public $category_slug;

    public function rules()
    {
        return [
            [["id", "slug", "title", "category_slug", "body", "view", "category_id", "thumbnail_base_url", "thumbnail_path", "status", "created_at"], "safe"]
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find()->published()
            ->joinWith(["category"])
            ->with(["articleSames"]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
            ],
        ]);
        if (!($this->load($params, "") && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "slug" => $this->slug,
            "title" => $this->title,
            "body" => $this->body,
            "view" => $this->view,
            "category_id" => $this->category_id,
            "thumbnail_base_url" => $this->thumbnail_base_url,
            "thumbnail_path" => $this->thumbnail_path,
            "created_at" => $this->created_at,
            "article.status" => $this->status,
            "article_category.slug" => $this->category_slug
        ]);
        return $dataProvider;
    }

    public function searchFind($params)
    {
        $query = Article::find()
            ->joinWith("category")
            ->published();
        $this->load($params, "");
        if (!$this->validate()) {
            return $query->one();
        }
        $query->andFilterWhere([
            "{{%article}}.id" => $this->id,
            "{{%article}}.slug" => $this->slug
        ]);
        return $query->one();
    }
}
