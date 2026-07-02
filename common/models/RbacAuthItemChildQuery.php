<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[RbacAuthItemChild]].
 *
 * @see RbacAuthItemChild
 */
class RbacAuthItemChildQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RbacAuthItemChild[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RbacAuthItemChild|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
