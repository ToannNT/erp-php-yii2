<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Color]].
 *
 * @see Color
 */
class ColorQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Color[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Color|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
