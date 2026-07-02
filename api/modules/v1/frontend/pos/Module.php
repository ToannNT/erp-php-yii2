<?php

namespace api\modules\v1\frontend\pos;

use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::class,
            'except' => ['services/auth/login', 'order-ship-web/print-bill'],
        ];
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'except' => ["user/info", "order-ship-web/print-bill"],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['manager', 'administrator', 'seller'],
                ]
            ]
        ];
        return $behaviors;
    }
}
