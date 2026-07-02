<?php

namespace common\models;

use Yii;
use \common\models\base\Article as BaseArticle;
use yii\helpers\ArrayHelper;

/**
 * @property User $author
 * @property User $updater
 * @property ArticleCategory $category
 * @property ArticleAttachment[] $articleAttachments
 * This is the model class for table "article".
 */
class Article extends BaseArticle
{

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;


    /**
     * @var array
     */
    public $attachments;

    /**
     * @var array
     */
    public $thumbnail;

    /**
     * @return array statuses list
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => Yii::t('common', 'Draft'),
            self::STATUS_PUBLISHED => Yii::t('common', 'Published'),
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function formName()
    {
        return "";
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
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getAuthorName()
    {
        return $this->author->username ?? "";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater(): string
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ArticleCategory::class, ['id' => 'category_id']);
    }

    public function getCategoryName(): string
    {
        return $this->category->title;
    }

    public function getCategorySlug()
    {
        return $this->category->slug;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleAttachments()
    {
        return $this->hasMany(ArticleAttachment::class, ['article_id' => 'id']);
    }
}
