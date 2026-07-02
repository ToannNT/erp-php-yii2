<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[SubDepartment]].
 *
 * @see SubDepartment
 */
class SubDepartmentQuery extends \common\models\base\ActiveQuery
{
    public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(["<>", "sub_department.status", SubDepartment::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return SubDepartment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SubDepartment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
