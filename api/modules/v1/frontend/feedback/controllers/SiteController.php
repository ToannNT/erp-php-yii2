<?php

namespace api\modules\v1\frontend\feedback\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\feedback\models\form\SaveForm;
use Yii;

class SiteController extends \yii\rest\Controller
{
    public function verbs(): array
    {
        return [
            "create" => ["POST"]
        ];
    }

    /**
     * @return array
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionCreate(): array
    {
        $feedback = new SaveForm();
        $feedback->load(Yii::$app->request->post(), "");
        if (!$feedback->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $feedback->getErrors()
            ]);
        }
        $userLogged = Yii::$app->user->identity;
        $feedback->user_id = $userLogged ? $userLogged->getId() : null;
        $feedback->save();
        return ResponseBuilder::responseJson(true,
            compact("feedback"),
            Yii::t("api", "Create {module} success")
        );
    }
}
