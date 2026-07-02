<?php
namespace api\modules\v1\frontend\banner\controllers;
use yii\rest\controller as BaseController;

class Controller extends BaseController{
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
