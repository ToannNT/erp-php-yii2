<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OfficePolicy]].
 *
 * @see OfficePolicy
 */
class OfficePolicyQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function unDelete()
    {
        $this->andWhere(["<>", "office_policy.status", OfficePolicy::STATUS_DELETE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return OfficePolicy[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OfficePolicy|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
