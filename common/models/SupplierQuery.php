<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Supplier]].
 *
 * @see Supplier
 */
class SupplierQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['supplier.status' => Inventory::STATUS_ACTIVE]);
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(['<>', 'supplier.status', Supplier::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Supplier[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Supplier|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
