<?php

namespace api\modules\v1\admin;

use common\models\User;
use yii\base\Module;

class AdminModule extends Module
{
    public function init()
    {
        $this->modules = [
            "setting" => setting\Module::class,
            "services" => services\Module::class,
            "product" => product\Module::class,
            "person" => person\Module::class,
            "inventory" => inventory\Module::class,
            "order" => order\Module::class,
            "general" => general\Module::class,
            "report" => report\Module::class,
            "order-return" => order_return\Module::class,
            "article" => article\Module::class,
            "cms" => cms\Module::class,
            "feedback" => feedback\Module::class,
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::class,
            'except' => ['services/auth/login', 'services/auth/login-newzen'],
        ];
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'except' => ['services/auth/login', 'services/auth/login-newzen', 'order-return/*'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR, User::ROLE_SUPPLIER, User::ROLE_STAFF],
                ]
            ]
        ];
        return $behaviors;
    }
}
