<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ProductVariant]].
 *
 * @see ProductVariant
 */
class ProductVariantQuery extends \common\models\base\ActiveQuery
{
    public function active()
    {
        $this->andWhere(["status" => ProductVariant::STATUS_ACTIVE]);
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(["!=", "{{product_variant}}.status", ProductVariant::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return ProductVariant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProductVariant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
