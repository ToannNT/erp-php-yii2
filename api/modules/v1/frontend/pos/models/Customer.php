<?php

namespace api\modules\v1\frontend\pos\models;

use common\models\Customer as CustomerBase;

class Customer extends CustomerBase
{
    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "phone",
            "address_1"
        ];
    }
}
