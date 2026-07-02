<?php

namespace api\modules\v1\frontend\product\controllers;

use yii\rest\Controller as BaseController;

class Controller extends BaseController
{
    public function verbs()
    {
        return [
            'index' => ['GET'],
            'update' => ['POST'],
            'view' => ['GET'],
            'delete' => ['POST', 'DELETE'],
            'delete-many' => ['POST', 'DELETE'],
            'create' => ['POST']
        ];
    }
}
