<?php

namespace api\modules\v1\frontend\product\models;

use common\behaviors\JsonBehavior;
use common\models\Brand as BaseBrand;

class Brand extends BaseBrand
{
    public function fields()
    {
        return [
            "id",
            "name",
            "icon" => "firstIcon",
            "slug",
        ];
    }

    public function getFirstIcon()
    {
        return is_array($this->icon) ? current($this->icon) : null;
    }
}