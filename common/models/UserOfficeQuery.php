<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserOffice]].
 *
 * @see UserOffice
 */
class UserOfficeQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return UserOffice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserOffice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
