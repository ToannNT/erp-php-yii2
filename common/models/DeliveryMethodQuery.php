<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DeliveryMethod]].
 *
 * @see DeliveryMethod
 */
class DeliveryMethodQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(["status" => DeliveryMethod::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function unDelete()
    {
        $this->andWhere(["<>", "status", DeliveryMethod::STATUS_DELETE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function isDefault()
    {
        $this->andWhere(["is_default" => 1]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return DeliveryMethod[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DeliveryMethod|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
