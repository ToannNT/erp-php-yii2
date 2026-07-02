<?php

namespace api\modules\v1\admin\services\controllers;

use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use yii\rest\Controller;
use yii\web\Response;

class StorageController extends Controller
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['manager', 'administrator'],
                ]
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => UploadAction::class,
                'multiple' => true,
                'disableCsrf' => false,
                'responseFormat' => Response::FORMAT_JSON,
                'responsePathParam' => 'path',
                'responseDeleteUrlParam' => 'delete_url',
                'responseMimeTypeParam' => 'type',
                'responseNameParam' => 'name',
                'responseSizeParam' => 'size',
                'deleteRoute' => 'upload-delete',
                'fileStorage' => 'fileStorage',
                'fileStorageParam' => 'fileStorage',
                'validationRules' => [
                    [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg ,jpeg,webp,svg']
                ],
            ],
            'upload-delete' => [
                'class' => DeleteAction::class,
            ],
        ];
    }
}
