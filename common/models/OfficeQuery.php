<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Office]].
 *
 * @see Office
 */
class OfficeQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => Inventory::STATUS_ACTIVE]);
        return $this;
    }

    public function unDelete()
    {
        $this->andWhere(["<>", "office.status", Office::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Office[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Office|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
