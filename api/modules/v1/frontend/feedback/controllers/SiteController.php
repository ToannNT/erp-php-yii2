<?php

namespace api\modules\v1\frontend\feedback\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\feedback\models\Feedback;
use api\modules\v1\frontend\feedback\models\form\SaveForm;
use api\modules\v1\frontend\feedback\models\search\FeedbackSearch;
use Yii;
use yii\web\NotFoundHttpException;

class SiteController extends \yii\rest\Controller
{
    public function verbs(): array
    {
        return [
            'create' => ['POST'],
            'index'  => ['GET'],
            'view'   => ['GET'],
        ];
    }
    public function actionCreate(): array
    {
        $feedback = new SaveForm();
        $feedback->load(Yii::$app->request->post(), '');
        if (!$feedback->validate()) {
            return ResponseBuilder::responseJson(false, [
                'errors' => $feedback->getErrors()
            ]);
        }
        $userLogged = Yii::$app->user->identity;
        $feedback->user_id = $userLogged ? $userLogged->getId() : null;
        if (!$feedback->save(false)) {
            return ResponseBuilder::responseJson(false, ['errors' => $feedback->getErrors()]);
        }
        return ResponseBuilder::responseJson(
            true,
            compact('feedback'),
            Yii::t('api', 'Create {module} success')
        );
    }


    public function actionIndex(): array
    {
        $search = new FeedbackSearch();
        return ResponseBuilder::responseJson(true, $search->search(Yii::$app->request->queryParams));
    }

    public function actionView(int $id): array
    {
        $feedback = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact('feedback'));
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
