<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Category]].
 *
 * @see Category
 */
class CategoryQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Category[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Category|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        $this->andWhere(["status" => Category::STATUS_ACTIVE]);
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(["<>", "status", Category::STATUS_DELETE]);
        return $this;
    }
}
