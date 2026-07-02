<?php

namespace api\modules\v1\admin\inventory\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\inventory\models\search\InventorySearch;
use Yii;
use yii\rest\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return ResponseBuilder::responseJson(true, (new InventorySearch())->search(Yii::$app->request->queryParams));
    }
}
