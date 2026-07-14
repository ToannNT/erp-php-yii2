<?php

namespace api\modules\v1\admin\cms\models;


use common\models\SystemCmsCollection;

class Collection extends \common\models\Collection
{
    public function formName()
    {
        return "";
    }

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

    public function extraFields()
    {
        $extrasFields = [];
        $schemas = self::$schemas;
        foreach ($schemas as $schema) {
            if ($schema["type"] == SystemCmsCollection::TYPE_RELATION) {
                continue;
            }
            $extrasFields[] = $schema["name"];
        }
        return $extrasFields;
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
