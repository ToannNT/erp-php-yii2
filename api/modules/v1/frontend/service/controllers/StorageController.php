<?php

namespace api\modules\v1\frontend\service\controllers;

use yii\rest\Controller;
use trntv\filekit\actions\UploadAction;

class StorageController extends Controller
{
    public function actions()
    {
        return [
            'upload' => [
                'class' => UploadAction::class,
                'multiple' => true,
                'disableCsrf' => true,
                'responsePathParam' => 'path',
                'responseBaseUrlParam' => 'base_url',
                'responseUrlParam' => 'url',
                'responseDeleteUrlParam' => 'delete_url',
                'responseMimeTypeParam' => 'type',
                'responseNameParam' => 'name',
                'responseSizeParam' => 'size',
                'deleteRoute' => 'delete',
                'fileStorage' => 'fileStorage',
                'fileStorageParam' => 'fileStorage',
                'sessionKey' => '_uploadedFiles',
                'allowChangeFilestorage' => false,
                'validationRules' => [
                    [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg ,jpeg']
                ]
            ]
        ];
    }
}
