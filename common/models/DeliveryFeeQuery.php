<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DeliveryFee]].
 *
 * @see DeliveryFee
 */
class DeliveryFeeQuery extends \common\models\base\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => DeliveryFee::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return DeliveryFee[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    public function unDelete()
    {
        $this->andWhere(["<>", "status", DeliveryFee::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return DeliveryFee|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
