<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserToken]].
 *
 * @see UserToken
 */
class UserTokenQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return UserToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \common\models\query\UserTokenQuery
     */
    public function notExpired()
    {
        $this->andWhere(['>', 'expire_at', time()]);
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function byType($type)
    {
        $this->andWhere(['type' => $type]);
        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    public function byToken($token)
    {
        $this->andWhere(['token' => $token]);
        return $this;
    }
}
