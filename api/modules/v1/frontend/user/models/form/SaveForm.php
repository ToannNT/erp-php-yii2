<?php

namespace api\modules\v1\frontend\user\models\form;

use api\modules\v1\frontend\user\models\User;

class SaveForm extends User
{
    public function fields()
    {
        return [
            "id",
            "access_token",
            "username",
            "email",
            "created_at"
        ];
    }


    public function rules(): array
    {
        return [
            ["password_hash", "safe"],
            ["email", "unique", "filter" => [
                "!=", "status", self::STATUS_DELETE
            ], "on" => "update"],
            ["username", "unique", "filter" => [
                "!=", "status", self::STATUS_DELETE
            ], "on" => "update"],
            ["email", "email"],
            [["email", "username", "password"], "required", "on" => "update"],
            [["password"], "required"],
            [["status"], "default", "value" => self::STATUS_INACTIVE, "on" => "update"]
        ];
    }
}
