<?php

namespace api\modules\v1\frontend\cart\controllers;

class Controller extends \yii\rest\Controller
{
    public function verbs(): array
    {
        return [
//            'add' => ['POST'],
            'index' => ['GET'],
            'update' => ['POST'],
            'view' => ['GET'],
            'delete' => ['POST', 'DELETE'],
            'create' => ['POST']
        ];
    }
}