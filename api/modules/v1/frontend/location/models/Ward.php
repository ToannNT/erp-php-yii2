<?php

namespace api\modules\v1\frontend\location\models;

use common\models\Ward as BaseWard;

class Ward extends BaseWard
{
    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return [
            "code",
            "full_name"
        ];
    }
}