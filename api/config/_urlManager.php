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
        // Sitemap sources cho Nuxt (@nuxtjs/sitemap) — trả mảng thô [{loc, lastmod}]
        'GET,OPTIONS api/v1/frontend/sitemap/products' => 'api/v1/frontend/sitemap/default/products',
        'GET,OPTIONS api/v1/frontend/sitemap/categories' => 'api/v1/frontend/sitemap/default/categories',
        'GET,OPTIONS api/v1/frontend/sitemap/brands' => 'api/v1/frontend/sitemap/default/brands',
        'GET,OPTIONS api/v1/frontend/sitemap/articles' => 'api/v1/frontend/sitemap/default/articles',
        'GET,OPTIONS api/v1/frontend/sitemap/pages' => 'api/v1/frontend/sitemap/default/pages',
        // Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/article', 'only' => ['index', 'view', 'options']],
//        [
//            'pattern' => 'api/v1/admin/cms/record/<collection_name>',
//            'route'   => 'api/v1/admin/cms/record',
//            'suffix'  => '',
//        ]
    ]
];
