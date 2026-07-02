<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[RbacAuthRule]].
 *
 * @see RbacAuthRule
 */
class RbacAuthRuleQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RbacAuthRule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RbacAuthRule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
