<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[I18nMessage]].
 *
 * @see I18nMessage
 */
class I18nMessageQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return I18nMessage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return I18nMessage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
