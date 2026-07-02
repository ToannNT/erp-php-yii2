<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CommentLike]].
 *
 * @see CommentLike
 */
class CommentLikeQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CommentLike[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CommentLike|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
