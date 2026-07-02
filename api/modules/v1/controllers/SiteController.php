<?php

namespace api\modules\v1\controllers;


/**
 * Class SiteController
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package api\modules\v1\controllers
 */
class SiteController extends Controller
{
//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//        $behaviors['authenticator'] = [
//            'class' => CompositeAuth::class,
//            'authMethods' => [
//                HttpBearerAuth::class,
//            ]
//        ];
//        return $behaviors;
//    }

    public function actionIndex()
    {
        return 1;
    }
}