<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[InventoryIssue]].
 *
 * @see InventoryIssue
 */
class InventoryIssueQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['!=', 'inventory_issue.status', InventoryIssue::STATUS_DELETE]);
        return $this;
    }

    public function notPending()
    {
        return $this->andWhere(["<>", "status", InventoryIssue::STATUS_PENDING]);
    }

    public function pending()
    {
        return $this->andWhere(["inventory_issue.status" => InventoryIssue::STATUS_PENDING]);
    }

    public function notCancel()
    {
        return $this->andWhere(["<>", "status", InventoryIssue::STATUS_CANCEL]);
    }

    public function notDelivery()
    {
        return $this->andWhere(["<>", "status", InventoryIssue::STATUS_RECEIVE]);
    }

    public function notInStatus($status)
    {
        return $this->andWhere(["<>", "status", $status]);
    }

    public function notDone()
    {
        return $this->andWhere(["<>", "status", InventoryIssue::STATUS_DONE]);
    }

    /**
     * @inheritdoc
     * @return InventoryIssue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InventoryIssue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
