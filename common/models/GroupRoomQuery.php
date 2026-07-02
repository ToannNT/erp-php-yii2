<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[GroupRoom]].
 *
 * @see GroupRoom
 */
class GroupRoomQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return GroupRoom[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GroupRoom|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
