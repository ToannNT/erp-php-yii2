<?php

namespace api\modules\v1\admin\order\models;

use common\models\Promotion as BasePromotion;

class Promotion extends BasePromotion
{

    public function fields()
    {
        return [
            "id",
            "title",
            "code",
            "discount_type",
            "discount_value",
            "condition_items" => function () {
                return json_decode($this->condition_items);
            },
            "condition_type",
            "offices" => function () {
                return json_decode($this->offices);
            },
            "limit",
            "created_at",
            "updated_at"
        ];
    }

    public function formName()
    {
        return "";
    }
}
