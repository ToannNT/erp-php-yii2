<?php

namespace common\models;

class UserAuthencation extends \yii\web\User
{
    public function canIn(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }
}
