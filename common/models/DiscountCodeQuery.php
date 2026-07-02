<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DiscountCode]].
 *
 * @see DiscountCode
 */
class DiscountCodeQuery extends \common\models\base\ActiveQuery
{
    public function active()
    {
        $this->andWhere(["status" => DiscountCode::STATUS_ACTIVE])
            ->andWhere([">", "expired_at", date("Y-m-d h:i:s")])
            ->andWhere([">", "limit", 0]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return DiscountCode[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DiscountCode|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
