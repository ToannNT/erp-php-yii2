<?php

namespace common\base\cms\models;

use common\models\Category as BaseCategory;

class Category extends BaseCategory
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