<?php

namespace common\behaviors;

use common\models\base\ActiveRecord;
use yii\base\Behavior;
use yii\base\Model;
use yii\db\BaseActiveRecord;

class JsonBehavior extends Behavior
{
    public $jsonAttributes;
    public $eventMaps = [];

    public function events()
    {
        return array_merge([
            BaseActiveRecord::EVENT_BEFORE_INSERT => "encode",
            BaseActiveRecord::EVENT_BEFORE_UPDATE => "encode",
            BaseActiveRecord::EVENT_AFTER_INSERT => "decode",
            BaseActiveRecord::EVENT_AFTER_UPDATE => "decode",
            BaseActiveRecord::EVENT_AFTER_FIND => "decode"
        ], $this->eventMaps);
    }

    public function encode()
    {
        foreach ($this->jsonAttributes as $jsonAttribute) {
            if (is_array($this->owner->$jsonAttribute)) {
                $this->owner->$jsonAttribute = json_encode($this->owner->$jsonAttribute, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function decode()
    {
        foreach ($this->jsonAttributes as $jsonAttribute) {
            if (is_string($this->owner->$jsonAttribute)) {
                $this->owner->$jsonAttribute = json_decode($this->owner->$jsonAttribute, true);
            }
        }
    }
}