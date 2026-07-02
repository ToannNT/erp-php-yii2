<?php

namespace api\modules\v1\admin\cms\models;


use common\models\SystemCmsCollection;

class Collection extends \common\models\Collection
{
    public function formName()
    {
        return "";
    }

    public function extraFields()
    {
        $extrasFields = [];
        $schemas = self::$schemas;
        foreach ($schemas as $schema) {
            if ($schema["type"] != SystemCmsCollection::TYPE_RELATION) {
                $extrasFields[] = $schema["name"];
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