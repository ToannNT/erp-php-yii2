<?php

namespace api\modules\v1\admin\setting;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init()
    {
        parent::init();
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'except' => ["supplier/*", "sub-department/*", "department/*", "delivery-fee/*", "office/*", "inventory/*", "office-policy/*", "promotion/*", "user/index"],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['administrator'],
                ]
            ]
        ];
        return $behaviors;
    }
}
