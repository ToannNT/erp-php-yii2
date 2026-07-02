<?php

namespace api\modules\v1\admin\article\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\article\models\Article;
use api\modules\v1\admin\article\models\search\ArticleSearch;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return ResponseBuilder::responseJson(true, (new ArticleSearch())->search(\Yii::$app->request->queryParams));
    }

    public function actionView($id)
    {
        $article = Article::find()->where(["id" => $id])->one();
        if (!$article) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return ResponseBuilder::responseJson(true, compact("article"));
    }
}