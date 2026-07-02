<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Comment]].
 *
 * @see Comment
 */
class CommentQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Comment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Comment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active(): CommentQuery
    {
        return $this->andWhere(["status" => Comment::STATUS_ACTIVE]);
    }

    public function product()
    {
        return $this->andWhere(["type" => Comment::TYPE_PRODUCT]);
    }

    public function post()
    {
        return $this->andWhere(["type" => Comment::TYPE_POST]);
    }
}
