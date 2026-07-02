<?php

namespace api\modules\v1\admin\order\controllers;

use api\modules\v1\behaviors\TestBehavior;
use yii\rest\Controller;

class  TestController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                "class" => TestBehavior::className(),
                "value" => 2
            ]
        ]);
    }

    public function actionTest()
    {
        var_dump($this->id);
    }

}