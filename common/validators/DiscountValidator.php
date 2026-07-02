<?php

namespace common\validators;

use yii\validators\Validator;

class DiscountValidator extends Validator
{

    const DISCOUNT_PRICE = 2;
    const DISCOUNT_PERCENT = 1;

    public $discountType;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    public function validateAttribute($model, $attribute)
    {
        if ($model->{$this->discountType} == self::DISCOUNT_PERCENT) {
            if ($model->{$attribute} > 100) {
                $this->addError($model, $attribute, "{attribute} Discount Type invalid");
            }
        }
    }
}
