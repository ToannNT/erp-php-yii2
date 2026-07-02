<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Product]].
 *
 * @see Product
 */
class ProductQuery extends \common\models\base\ActiveQuery
{
    public function active()
    {
        $this->andWhere(["{{product}}.status" => Product::STATUS_ACTIVE]);
        return $this;
    }

    public function allow_sell(): ProductQuery
    {
        return $this->andWhere(["allow_sell" => Product::STATUS_ACTIVE]);
    }

    public function unDelete()
    {
        return $this->andWhere(["<>", "{{product}}.status", Product::STATUS_DELETE]);
    }

    /**
     * @inheritdoc
     * @return Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
