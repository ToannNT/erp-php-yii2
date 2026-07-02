<?php

namespace api\modules\v1\admin\general\models;

use common\models\Office as BaseOffice;

class Office extends BaseOffice
{

    public function fields()
    {
        return [
            "id",
            "name"
        ];
    }

    public function formName()
    {
        return "";
    }
}
