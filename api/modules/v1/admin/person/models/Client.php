<?php

namespace api\modules\v1\admin\person\models;

use common\models\Customer;

class Client extends Customer
{
    public function fields()
    {
        return [
            "id",
            "code",
            "name",
            "email",
            "phone",
            "status",
            "address_1",
            "created_at",
            "updated_at"
        ];
    }

    public function formName()
    {
        return "";
    }
}
