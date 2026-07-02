<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Department]].
 *
 * @see Department
 */
class DepartmentQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function active()
    {
        $this->andWhere(["department.status" => Department::STATUS_ACTIVE]);
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(["<>", "department.status", Department::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Department[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Department|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
