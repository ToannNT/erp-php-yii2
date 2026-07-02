<?php

namespace common\models;

use \common\models\base\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ArticleAttachment]].
 *
 * @see ArticleAttachment
 */
class ArticleAttachmentQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => ArticleCategory::STATUS_ACTIVE]);

        return $this;
    }

    /**
     * @return $this
     */
    public function noParents()
    {
        $this->andWhere('{{%article_category}}.parent_id IS NULL');

        return $this;
    }
}
