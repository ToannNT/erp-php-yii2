<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Brand]].
 *
 * @see Brand
 */
class BrandQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Brand[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Brand|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->andWhere(["brand.status" => Brand::STATUS_ACTIVE]);
    }

    public function unDelete()
    {
        return $this->andWhere(["<>", "status", Brand::STATUS_DELETE]);
    }
}
