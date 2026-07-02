<?php

namespace common\validators;

//use SamIT\Yii2\Components\Map;
use Yii;
use yii\validators\Validator;

class IsArrayValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('common', '"{attribute}" must be a valid Array');
        }
    }

    public function validateAttribute($model, $attribute): bool
    {
        if (!is_array($model->$attribute)) {
            $this->addError($model, $attribute, $this->message);
            return false;
        }
        return true;
    }
}
