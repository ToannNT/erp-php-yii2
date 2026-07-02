<?php

namespace api\modules\v1\frontend\user\models\form;

use api\modules\v1\frontend\user\models\User;
use Yii;

class LoginForm extends User
{
    public $password;

    public function rules(): array
    {
        return [
            [["password", "email"], "required"],
        ];
    }

    public function fields()
    {
        return [
            "id",
            "username",
            "email",
            "token",
            "logged_at",
            "created_at",
            "role" => function (User $model) {
                foreach (Yii::$app->authManager->getRolesByUser($model->id) as $item) {
                    return $item->name;
                }
            }
        ];
    }
}
