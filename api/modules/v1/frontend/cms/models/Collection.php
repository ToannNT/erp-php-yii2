<?php

namespace api\modules\v1\frontend\cms\models;

use common\models\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function extraFields()
    {
        $extrasFields = [];
        $schemas = self::$schemas;
        foreach ($schemas as $schema) {
            if ($schema["type"] != SystemCmsCollection::TYPE_RELATION) {
                continue;
            }
            $extrasFields[$schema["name"]] = function () use ($schema) {
                if (!empty($schema["options"]["is_cms"])) {
                    return $this->getRelationCms($schema);
                }
                return $this->getRelationCore($schema);
            };
        }
        return $extrasFields;
    }
}