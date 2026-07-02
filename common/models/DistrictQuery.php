<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[District]].
 *
 * @see District
 */
class DistrictQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return District[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return District|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
