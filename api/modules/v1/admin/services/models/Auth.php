<?php

namespace api\modules\v1\admin\services\models;

use Yii;
use common\models\UserIdentity;

class Auth extends UserIdentity
{
    public $password;

    public function fields()
    {
        return [
            "id",
            "username",
            "token",
            "logged_at"
        ];
    }

    public function extraFields()
    {
        return [
            "role" => function (Auth $model) {
                foreach(Yii::$app->authManager->getRolesByUser($model->id) as $item){
                    return $item->name;
                }
            }
        ];
    }

    public function rules()
    {
        return [
            [["email", "password"], "required"]
        ];
    }

    public function login()
    {
        $user = self::findByLogin($this->email);
        if (!$user || !$user->validatePassword($this->password)) {
            return false;
        }
        // $permistions = Yii::$app->authManager->getPermissionsByUser($user->getId());
        // if (!array_key_exists("loginToBackend", $permistions)) {
        //     return false;
        // }
        $user->generateToken();
        return $user;
    }
}
