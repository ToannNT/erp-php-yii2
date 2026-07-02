<?php

namespace api\modules\v1\frontend\pos\controllers;

use yii\web\NotFoundHttpException;
use api\helper\response\ResponseBuilder;
use common\models\Office;
use yii\rest\Controller;

class GeneralController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionOfficeDetail($id)
    {
        $office = Office::findOne($id);
        if (empty($office)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return ResponseBuilder::responseJson(true, compact("office"));
    }
}