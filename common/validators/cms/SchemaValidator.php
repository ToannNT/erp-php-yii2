<?php

namespace common\validators\cms;

use common\models\SystemCmsCollection;
use yii\base\DynamicModel;
use yii\validators\Validator;

class SchemaValidator extends Validator
{
    /**
     * @param $model SystemCmsCollection
     * @param $attribute
     * @return void
     */
    public function validateAttribute($model, $attribute)
    {
        $schemas = json_decode($model->schemas, true);
        foreach ($schemas as $key => $schema) {
            $modelSchema = new DynamicModel(["name", "type", "validator", "options", "id"]);
            $modelSchema->addRule(['name', 'type'], 'required')
                ->addRule(['type'], 'in', ['range' => SystemCmsCollection::SCHEMA_TYPES])
                ->addRule(['validator', 'options', 'id'], 'safe')
                ->addRule(['validator'], SchemaValidateValidator::class)
                ->addRule(['type'], $this->validateType());
            $modelSchema->load($schema, "");
            $modelSchema->validate();
            if ($modelSchema->hasErrors()) {
                $this->addError($model, $attribute, json_encode($modelSchema->getErrors()));
            } else {
                $schemas[$key] = $modelSchema->getAttributes();
                if (empty($schemas[$key]["id"])) {
                    $schemas[$key]["id"] = uniqid();
                }
            }
        }
        $model->schemas = json_encode($schemas);
    }

    public function validateType()
    {
        return function ($attribute) {
            switch ($this->$attribute) {
                case SystemCmsCollection::TYPE_RELATION:
                    if (!isset($this->options["ref_table"], $this->options["is_cms"], $this->options["type"])) {
                        $this->addError($attribute, "{$attribute} option relation invalid");
                    }
                    break;
                case SystemCmsCollection::TYPE_FILE:
                    if (empty($this->options["type"])) {
                        $this->addError($attribute, "{$attribute} options invalid");
                    }
                    break;
                case SystemCmsCollection::TYPE_SELECT:
                    if (!isset($this->options["type"], $this->options["value"])) {
                        $this->addError($attribute, "{$attribute} select invalid");
                    }
            }
        };
    }
}