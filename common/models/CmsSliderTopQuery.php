<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CmsSliderTop]].
 *
 * @see CmsSliderTop
 */
class CmsSliderTopQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CmsSliderTop[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CmsSliderTop|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
