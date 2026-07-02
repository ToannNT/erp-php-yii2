<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Feedback]].
 *
 * @see Feedback
 */
class FeedbackQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Feedback[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Feedback|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
