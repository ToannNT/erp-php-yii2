<?php

namespace api\modules\v1\frontend\location\models;

use common\models\Province as BaseProvince;

class Province extends BaseProvince
{

    public function fields()
    {
        return [
            "code",
            "full_name"
        ];
    }

    public function formName()
    {
        return "";
    }

}