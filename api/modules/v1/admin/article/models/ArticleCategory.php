<?php

namespace api\modules\v1\admin\article\models;

use yii\db\Query;
use common\models\ArticleCategory as BaseArticleCategory;

class ArticleCategory extends BaseArticleCategory
{
    public function fields()
    {
        return [
            "id",
            "slug",
            "title",
            "status",
            "children" => "children",
            "created_at" => function ($model) {
                return date("Y-m-d H:i:s", $model->created_at);
            },
            "updated_at" => function ($model) {
                return date("Y-m-d H:i:s", $model->updated_at);
            }
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [["title"], "required"],
            ["title", "unique", 'filter' => [
                "!=", "status", BaseArticleCategory::STATUS_DELETE
            ]],
            ["status", "default", "value" => BaseArticleCategory::STATUS_ACTIVE],
            ["status", "in", "range" => [BaseArticleCategory::STATUS_ACTIVE, BaseArticleCategory::STATUS_INACTIVE]],
            ["parent_id", "exist", "targetClass" => BaseArticleCategory::class, "filter" => function (Query $query) {
                $query->andWhere([
                    "parent_id" => null,
                    "status" => BaseArticleCategory::STATUS_ACTIVE
                ]);
            }, 'targetAttribute' => ['parent_id' => 'id'], "when" => function () {
                return $this->parent_id !== BaseArticleCategory::PARENT_ID_DEFAULT;
            }]
        ]);
    }

    public function getChildren()
    {
        return $this->hasMany(ArticleCategory::class, ["id" => "parent_id"]);
    }
}