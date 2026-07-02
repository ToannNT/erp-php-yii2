<?php

namespace api\modules\v1\controllers;

use api\modules\v1\behaviors\AuthBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Controller
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package api\modules\v1\controllers
 */
class Controller extends \yii\rest\Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => AuthBehavior::class,
            ],
        ]);
    }
}