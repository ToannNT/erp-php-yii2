<?php

namespace api\modules\v1\admin\order;

use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors["access_control_other"] = [
            'class' => AccessControl::class,
            'except' => ['website/*', 'promotion/*'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ["manager", "administrator"]
                ]
            ]
        ];
        return $behaviors;
    }
}