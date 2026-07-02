<?php

namespace api\modules\v1\frontend\comment\controllers;

use api\helper\response\ApiConstant;
use Yii;
use api\modules\v1\frontend\comment\models\{
    form\CommentForm,
    search\CommentSearch,
    Comment
};
use api\helper\response\ResponseBuilder;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors["authenticator"] = [
            'class' => CompositeAuth::class,
            'only' => ['create', 'update', 'delete'],
            'authMethods' => [
                HttpBearerAuth::class
            ]];
        return $behaviors;
    }

    /**
     * @throws yii\web\HttpException
     */
    public function actionIndex(): array
    {
        $model = new CommentSearch();
        $commentProvider = $model->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $commentProvider);
    }

    /**
     * @throws yii\web\HttpException
     */
    public function actionCreate(): array
    {
        $comment = new CommentForm();
        $comment->load(Yii::$app->request->post(), "");
        if (!$comment->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $comment->getErrors()
            ]);
        }
        $comment->user_id = Yii::$app->user->identity->getId();
        $comment->save();
        return ResponseBuilder::responseJson(true, compact("comment"));
    }


    /**
     * @throws yii\web\HttpException
     * Here method get comment by id and user_id
     * after update database
     * @author khuongdev2001
     */
    public function actionUpdate(int $id): array
    {
        $comment = $this->findModelLogged($id);
        $comment->load(Yii::$app->request->post(), "");
        if (!$comment->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $comment->getErrors()
            ]);
        }
        $comment->save();
        return ResponseBuilder::responseJson(true,
            compact("comment"),
            Yii::t("api", "Update {module} success")
        );
    }

    /**
     * @throws Yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionDelete(int $id): array
    {
        $comment = $this->findModelLogged($id);
        $comment->status = Comment::STATUS_DELETE;
        $comment->save(false);
        return ResponseBuilder::responseJson(
            true,
            null, Yii::t("api", "Delete {module} success")
        );
    }

    /**
     * @throws Yii\web\HttpException
     */
    public function actionView(int $id): array
    {
        $comment = CommentSearch::find()->where(["id" => $id])->active()->one();
        return ResponseBuilder::responseJson(true, compact("comment"));
    }

    /**
     * @throws yii\web\HttpException
     */
    private function findModelLogged($id)
    {
        $comment = CommentForm::find()
            ->where(["id" => $id])
            ->andWhere(["user_id" => Yii::$app->user->identity->getId()])
            ->active()
            ->one();
        if (!$comment) {
            throw new \yii\web\NotFoundHttpException();
        }
        return $comment;
    }
}