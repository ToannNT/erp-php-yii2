<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[State]].
 *
 * @see State
 */
class StateQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return State[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return State|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
