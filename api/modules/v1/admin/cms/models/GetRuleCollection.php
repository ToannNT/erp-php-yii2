<?php

namespace api\modules\v1\admin\cms\models;

use common\validators\EmailValidator;
use yii\base\BaseObject;
use yii\base\Model;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\UniqueValidator;

class GetRuleCollection extends BaseObject
{
    const VALIDATOR_REQUIRED = "required";
    const VALIDATOR_MIN = "min";
    const VALIDATOR_MAX = "max";
    const VALIDATOR_UNIQUE = "unique";
    const VALIDATOR_EMAIL = "email";

    public function getRuleForm($schema): array
    {
        $validators = [];
        foreach ($schema["validator"] as $item) {
            $validator = $this->getValidator($schema, $item);
            if (!$validator) {
                continue;
            }
            $validators[] = $validator;
        }
        return $validators;
    }

    /**
     * @param $schema
     * @return void
     */
    public function getRuleSearch($schema)
    {

    }

    /**
     * @param $schema
     * @param $validator
     * @return array
     */
    public function getValidator($schema, $validator): array
    {
        switch ($validator["validator_name"]) {
            case self::VALIDATOR_REQUIRED :
                return [$schema["name"], RequiredValidator::class, "message" => $validator["message_error"]];
            case self::VALIDATOR_UNIQUE:
                return [$schema["name"], UniqueValidator::class, "message" => $validator["message_error"]];
            case self::VALIDATOR_EMAIL:
                return [$schema["name"], EmailValidator::class, "message" => $validator["message_error"]];
        }
        return [];
    }
}