<?php

namespace common\base\cms\models;

use common\models\Brand as BaseBrand;

class Brand extends BaseBrand
{
    public function fields()
    {
        return [
            "id",
            "name",
            "icon",
            "slug"
        ];
    }
}