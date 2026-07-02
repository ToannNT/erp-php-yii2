<?php

namespace api\modules\v1\frontend\pos\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\search\InventorySearch;
use Yii;
use yii\rest\Controller;

class InventoryController extends Controller
{
    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     * @return array
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true,
            (new InventorySearch())->search(Yii::$app->request->queryParams)
        );
    }
    
}
