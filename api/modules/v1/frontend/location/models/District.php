<?php

namespace api\modules\v1\frontend\location\models;

use common\models\District as BaseDistrict;

class District extends BaseDistrict
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