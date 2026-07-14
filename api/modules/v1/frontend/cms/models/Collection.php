<?php

namespace api\modules\v1\frontend\cms\models;

use common\models\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function fields()
    {
        $fields = parent::fields();
        foreach (self::$schemas as $schema) {
            if ($schema["type"] != SystemCmsCollection::TYPE_RELATION) {
                continue;
            }
            $fields[$schema["name"]] = $this->getRelationFieldClosure($schema);
        }
        return $fields;
    }

    protected function getRelationFieldClosure($schema)
    {
        return function () use ($schema) {
            if (!empty($schema["options"]["is_cms"])) {
                return $this->getRelationCms($schema);
            }
            return $this->getRelationCore($schema);
        };
    }
}