<?php

namespace common\validators;

use yii\validators\EmailValidator as BaseEmailValidator;

class EmailValidator extends BaseEmailValidator
{
    /*
     * @var string
     * @description overwrite properties
     */
    public $pattern = "/^[a-zA-Z0-9*]+([-._][a-zA-Z0-9*+\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/";
}