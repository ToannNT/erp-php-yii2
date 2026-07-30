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
            "images",
            "slug"
        ];
    }

    /**
     * `icon` đã được decode sẵn bởi JsonBehavior khai ở common\models\Category (key "json").
     * Thêm `images` vào cùng behavior đó để không trả về chuỗi JSON thô.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"]["jsonAttributes"] = ["icon", "images"];
        return $behaviors;
    }
}
