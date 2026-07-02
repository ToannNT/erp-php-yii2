<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Contact]].
 *
 * @see Contact
 */
class ContactQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function unDelete()
    {
        return $this->andWhere(["<>", "status", Contact::STATUS_DELETE]);
    }

    /**
     * @inheritdoc
     * @return Contact[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Contact|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
