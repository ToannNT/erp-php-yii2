<?php

namespace common\models;

use Yii;
use \common\models\base\TimelineEvent as BaseTimelineEvent;
use yii\helpers\ArrayHelper;

/**
 * Class TimelineEvent
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class TimelineEvent extends BaseTimelineEvent
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->data = @json_decode($this->data, true);
        parent::afterFind();
    }

    /**
     * @return string
     */
    public function getFullEventName()
    {
        return sprintf('%s.%s', $this->category, $this->event);
    }
}
