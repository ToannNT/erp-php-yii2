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
            "images",
            "slug"
        ];
    }

    /**
     * `icon` đã được decode sẵn bởi JsonBehavior khai ở common\models\Brand (key "json").
     * Thêm `images` vào cùng behavior đó để không trả về chuỗi JSON thô.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"]["jsonAttributes"] = ["icon", "images"];
        return $behaviors;
    }
}
