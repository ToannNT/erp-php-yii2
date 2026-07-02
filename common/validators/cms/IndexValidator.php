<?php

namespace common\validators\cms;

use yii\validators\Validator;
use common\models\SystemCmsCollection;

class IndexValidator extends Validator
{
    /**
     * @param $model SystemCmsCollection
     * @param $attribute
     * @return void
     */
    public function validateAttribute($model, $attribute)
    {
        $schemas = json_decode($model->schemas, true);
        $oldSchemas = $model->getOldAttributes("schemas");
        $listIndex = json_decode($model->indexs, true);
        if (is_string($oldSchemas)) {
            $schemas = array_merge($schemas, $oldSchemas);
        }
        foreach ($listIndex as $index) {
            if (!$this->searchSchemas($schemas, $index)) {
                $this->addError($model, "indexs", "{$index} invalid");
                break;
            }
        }
    }

    protected function searchSchemas(&$schemas, $index)
    {
        foreach ($schemas as $key => $schema) {
            if ($index === $schema["name"]) {
                unset($schemas[$key]);
                return true;
            }
        }
        return false;
    }
}