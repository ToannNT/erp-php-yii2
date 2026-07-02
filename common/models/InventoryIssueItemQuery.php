<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[InventoryIssueItem]].
 *
 * @see InventoryIssueItem
 */
class InventoryIssueItemQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return InventoryIssueItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InventoryIssueItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
