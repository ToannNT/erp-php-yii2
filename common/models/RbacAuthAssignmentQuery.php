<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[RbacAuthAssignment]].
 *
 * @see RbacAuthAssignment
 */
class RbacAuthAssignmentQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RbacAuthAssignment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RbacAuthAssignment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
