<?php

namespace api\modules\v1\admin\setting\models;

use common\models\DeliveryMethod as BaseDeliveryMethod;

class DeliveryMethod extends BaseDeliveryMethod
{
    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [["name", "code"], "required"],
            [["name"], "string", "max" => 200],
            [["code"], "string", "max" => 100],
            [["code"], "unique", "filter" => ["<>", "status", BaseDeliveryMethod::STATUS_DELETE]],
            [["fee"], "default", "value" => 0],
            [["fee"], "number", "min" => 0],
            // chỉ áp default lúc tạo mới — update không gửi thì giữ nguyên giá trị cũ
            [["is_default"], "default", "value" => 0, "when" => function ($model) {
                return $model->isNewRecord;
            }],
            [["is_default"], "boolean"],
            [["status"], "default", "value" => BaseDeliveryMethod::STATUS_ACTIVE, "when" => function ($model) {
                return $model->isNewRecord;
            }],
            [["status"], "in", "range" => [BaseDeliveryMethod::STATUS_ACTIVE, BaseDeliveryMethod::STATUS_INACTIVE]],
        ];
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "fee" => function () {
                return (float)$this->fee;
            },
            "status",
            "is_default" => function () {
                return (int)$this->is_default;
            },
            "created_by",
            "created_at",
            "updated_at",
        ];
    }
}
