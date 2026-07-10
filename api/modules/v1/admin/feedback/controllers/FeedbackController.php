<?php

namespace api\modules\v1\admin\feedback\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\feedback\models\Feedback;
use api\modules\v1\admin\feedback\models\search\FeedbackSearch;
use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class FeedbackController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR, User::ROLE_STAFF],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * GET api/v1/admin/feedback/feedback
     */
    public function actionIndex(): array
    {
        $search = new FeedbackSearch();
        return ResponseBuilder::responseJson(true, $search->search(Yii::$app->request->queryParams));
    }

    /**
     * GET api/v1/admin/feedback/feedback/view?id=1
     */
    public function actionView(int $id): array
    {
        $feedback = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact('feedback'));
    }

    /**
     * POST api/v1/admin/feedback/feedback/delete?id=1
     */
    public function actionDelete(int $id): array
    {
        $feedback = $this->findModel($id);
        if ($feedback->delete()) {
            return ResponseBuilder::responseJson(true, null, 'Delete feedback successfully');
        }
        return ResponseBuilder::responseJson(false, null, 'Cannot delete feedback');
    }

    protected function findModel(int $id): Feedback
    {
        $feedback = Feedback::findOne($id);
        if (!$feedback) {
            throw new NotFoundHttpException('Feedback not found');
        }
        return $feedback;
    }
}
