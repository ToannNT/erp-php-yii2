<?php

namespace common\base\cms\models;

use common\models\User as BaseUser;

class User extends BaseUser
{
    public function fields()
    {
        return [
            "id",
            "username"
        ];
    }
}