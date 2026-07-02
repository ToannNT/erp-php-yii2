<?php

namespace common\models;

use Yii;
use \common\models\base\ArticleAttachment as BaseArticleAttachment;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * @property Article $article
 * This is the model class for table "article_attachment".
 */
class ArticleAttachment extends BaseArticleAttachment
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false
            ]
        ];
    }

//    public function behaviors()
//    {
//        return ArrayHelper::merge(
//            parent::behaviors(),
//            [
//
//                # custom behaviors
//            ]
//        );
//    }

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
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }

    public function getUrl()
    {
        return $this->base_url . '/' . $this->path;
    }
}
