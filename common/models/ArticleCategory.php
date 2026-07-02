<?php

namespace common\models;

use Yii;
use \common\models\base\ArticleCategory as BaseArticleCategory;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "article_category".
 */
class ArticleCategory extends BaseArticleCategory
{
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 0;
    const STATUS_DELETE = -99;
    const PARENT_ID_DEFAULT = 0;


    /**
     * @return array statuses list
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => Yii::t('common', 'Draft'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETE => Yii::t("common", "Delete")
        ];
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'immutable' => true,
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'deleted_at' => time()
                ],
            ],
        ];
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ArticleCategory::class, ['id' => 'parent_id'])
            ->andWhere(["<>", "status", self::STATUS_DELETE]);
    }

    public function formName()
    {
        return "";
    }

    public function getChildren()
    {
        return $this->hasMany(ArticleCategory::class, ["id" => "parent_id"]);
    }
}
