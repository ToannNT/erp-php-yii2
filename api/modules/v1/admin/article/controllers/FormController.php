<?php

namespace api\modules\v1\admin\article\controllers;

use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\article\models\Article;
use yii\rest\Controller;

class FormController extends Controller
{
    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $article = new Article();
        $article->load(Yii::$app->request->post());
        if ($article->validate() && $article->save()) {
            return ResponseBuilder::responseJson(true, compact("article"), "Create Article successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $article->getErrors()], "Create Article fail");
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $article = Article::find()->where(["id" => $id])->one();
        if (!$article) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $article->load(Yii::$app->request->post());
        if ($article->validate() && $article->save()) {
            return ResponseBuilder::responseJson(true, compact("article"), "Update Article successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $article->getErrors()], "Update Article fail");
    }

    /**
     * @param $id
     * @return array
     * @throws \yii\web\HttpException
     */
    public function actionDelete($id)
    {
        $article = Article::find()->where(["id" => $id])->one();
        if (!$article) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $article->status = Article::STATUS_DELETE;
        if ($article->save(false)) {
            return ResponseBuilder::responseJson(true, [], "Delete Article successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $article->getErrors()], "Update Article fail");
    }
}