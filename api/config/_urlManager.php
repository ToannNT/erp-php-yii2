<?php
return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        'GET,OPTIONS api/v1/admin/cms/record/<collection_name>' => 'api/v1/admin/cms/record',
        'POST,OPTIONS api/v1/admin/cms/record/<collection_name>/create' => 'api/v1/admin/cms/record/create',
        'GET,OPTIONS api/v1/admin/cms/record/<collection_name>/view' => 'api/v1/admin/cms/record/view',
        'POST,OPTIONS api/v1/admin/cms/record/<collection_name>/update' => 'api/v1/admin/cms/record/update',
        'POST,OPTIONS api/v1/admin/cms/record/<collection_name>/delete' => 'api/v1/admin/cms/record/delete',
        'GET,OPTIONS api/v1/frontend/cms/record/<collection_name>/view' => 'api/v1/frontend/cms/record/view',
        'GET,OPTIONS api/v1/frontend/cms/record/<collection_name>' => 'api/v1/frontend/cms/record/index',
        // Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/article', 'only' => ['index', 'view', 'options']],
//        [
//            'pattern' => 'api/v1/admin/cms/record/<collection_name>',
//            'route'   => 'api/v1/admin/cms/record',
//            'suffix'  => '',
//        ]
    ]
];
