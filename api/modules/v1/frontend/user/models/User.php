<?php

namespace api\modules\v1\frontend\user\models;

use common\models\UserIdentity;

class User extends UserIdentity
{
    public $token;
    public $password;


}
