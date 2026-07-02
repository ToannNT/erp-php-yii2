<?php
/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
return [
    'id' => 'storage',
    'basePath' => dirname(__DIR__),
    'components' => [
        'urlManager'=>require(__DIR__.'/_urlManager.php')
    ]
];
