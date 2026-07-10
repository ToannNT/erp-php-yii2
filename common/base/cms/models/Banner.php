<?php

namespace common\base\cms\models;

use common\models\Banner as BaseBanner;

class Banner extends BaseBanner
{
    public function fields()
    {
        return [
            "id",
            "title",
            "url",
            "description",
            "type",
            "priority",
            "video_url",
            "link",
        ];
    }
}
