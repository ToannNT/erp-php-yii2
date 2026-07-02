<?php

namespace api\modules\v1\admin\person;

use common\models\User;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'except' => ['customer/*', 'contact/*'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR],
                ]
            ]
        ];
        return $behaviors;
    }

}