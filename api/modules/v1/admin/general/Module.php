<?php

namespace api\modules\v1\admin\general;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['manager', 'administrator', 'seller', 'supplier'],
                ]
            ]
        ];
        return $behaviors;
    }

}