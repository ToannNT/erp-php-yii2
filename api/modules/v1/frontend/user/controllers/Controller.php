<?php

namespace api\modules\v1\frontend\user\controllers;

use yii\rest\Controller as BaseController;

class Controller extends BaseController
{
    public function verbs()
    {
        return [
            'login' => ['POST'],
            'signup' => ['POST'],
        ];
    }
}
