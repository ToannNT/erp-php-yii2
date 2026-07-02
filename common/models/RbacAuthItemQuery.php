<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[RbacAuthItem]].
 *
 * @see RbacAuthItem
 */
class RbacAuthItemQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RbacAuthItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RbacAuthItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
