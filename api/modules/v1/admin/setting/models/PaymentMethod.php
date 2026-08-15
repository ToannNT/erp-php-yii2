<?php

namespace api\modules\v1\admin\setting\models;

use common\models\PaymentMethod as BasePaymentMethod;

class PaymentMethod extends BasePaymentMethod
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
            [["code"], "unique", "filter" => ["<>", "status", BasePaymentMethod::STATUS_DELETE]],
            // chỉ áp default lúc tạo mới — update không gửi thì giữ nguyên giá trị cũ
            [["is_default"], "default", "value" => 0, "when" => function ($model) {
                return $model->isNewRecord;
            }],
            [["is_default"], "boolean"],
            [["status"], "default", "value" => BasePaymentMethod::STATUS_ACTIVE, "when" => function ($model) {
                return $model->isNewRecord;
            }],
            [["status"], "in", "range" => [BasePaymentMethod::STATUS_ACTIVE, BasePaymentMethod::STATUS_INACTIVE]],
        ];
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "code",
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
