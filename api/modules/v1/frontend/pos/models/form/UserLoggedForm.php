<?php

namespace api\modules\v1\frontend\pos\models\form;

use common\models\User;

class UserLoggedForm extends User
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ["id", "safe"]
        ]);
    }

    public function fields(): array
    {
        parent::fields();
        return [
            "id",
            "username",
            "email",
            "created_at",
        ];
    }
    
}
