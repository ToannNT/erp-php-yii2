<?php

use common\components\filesystem\ClouldflareR2;
use common\components\filesystem\LocalFlysystemBuilder;
use common\components\log\DbTarget;

// Chọn nơi lưu file upload: 'local' = ổ đĩa server (persist qua volume/persistent dir), còn lại = Cloudflare R2.
$isLocalStorage = env('FILE_STORAGE_DRIVER') === 'local';

$config = [
    'components' => [
        'assetManager' => [
            'class' => yii\web\AssetManager::class,
            // 'linkAssets' => env('LINK_ASSETS'),
            // 'appendTimestamp' => YII_ENV_DEV
        ],
        'fileStorage' => [
            'class' => trntv\filekit\Storage::class,
            'baseUrl' => $isLocalStorage ? env('UPLOAD_BASE_URL') : env('S3_BASE_URL'),
            'filesystem' => $isLocalStorage
                ? [
                    'class' => LocalFlysystemBuilder::class,
                    'path' => env('UPLOAD_PATH'),
                ]
                : [
                    'class' => ClouldflareR2::class,
                    'key' => env('S3_KEY'),
                    'secret' => env('S3_SECRET'),
                    'region' => env('S3_REGION'),
                    'bucket' => env('S3_BUCKET'),
                    'end_point' => env('S3_END_POINT')
                ],
            'as log' => [
                'class' => common\behaviors\FileStorageLogBehavior::class,
                'component' => 'fileStorage'
            ]
        ],
        'shipper' => [
            'class' => common\components\shipping\Client::class
        ],
        'log' => [
            'targets' => [
//                [
//                    'class' => 'yii\log\DbTarget',
//                    'logTable' => 'system_log',
//                    'categories' => ['api\*'],
//                    'levels' => ['info'],
//                ],
                [
                    'class' => DbTarget::className(),
                    'logTable' => 'history_log',
                    'categories' => ['api\*'],
                    'levels' => ['info'],
                    'logVars' => [null],
                ]
            ],
        ],
        'build_log' => [
            'class' => common\components\log\BuildLogDbTarget::class
        ],
    ]
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['*'],
    ];
}

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'allowedIPs' => ['*'],
    ];
}

return $config;
