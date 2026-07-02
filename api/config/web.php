<?php

use api\modules\Module;
use common\components\log\SentryErrorHandler;

$config = [
    'homeUrl' => Yii::getAlias('@apiUrl'),
    'controllerNamespace' => 'api\controllers',
    'defaultRoute' => 'site/index',
    'bootstrap' => ['maintenance'],
    'modules' => [
        'api' => Module::className()
    ],
    'components' => [
        'errorHandler' => [
            'class' => SentryErrorHandler::class,
            'dsn' => env("SENTRY_DSN"),
            'errorAction' => 'site/error'
        ],
        'maintenance' => [
            'class' => common\components\maintenance\Maintenance::class,
            'enabled' => function ($app) {
                // if (env('APP_MAINTENANCE') === '1') {
                //     return true;
                // }
                // return $app->keyStorage->get('frontend.maintenance') === 'enabled';
            }
        ],
        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'user' => [
            'class' => common\models\UserAuthencation::class,
            'identityClass' => common\models\User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
            'as afterLogin' => common\behaviors\LoginTimestampBehavior::class
        ],
    ]
];

return $config;
