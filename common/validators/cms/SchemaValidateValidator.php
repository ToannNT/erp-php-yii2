<?php

namespace common\validators\cms;

use yii\validators\Validator;

class SchemaValidateValidator extends Validator
{

    public function validateAttribute($model, $attribute)
    {
        foreach ($model->{$attribute} as $validator) {
            if (empty($validator["validator_name"]) || !isset($validator["message_error"])) {
                $this->addError($model, $attribute, "Invalid Validator");
                return false;
            }
            switch ($validator["validator_name"]) {
                case "max":
                case "min":
                    if (!isset($validator["value"])) {
                        $this->addError($model, $attribute, "Invalid Validator Max or Min");
                        return false;
                    }
                    break;
            }
        }
    }
}