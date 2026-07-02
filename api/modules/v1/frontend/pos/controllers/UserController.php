<?php

namespace api\modules\v1\frontend\pos\controllers;

use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\form\UserLoggedForm;
use yii\rest\Controller;

class UserController extends Controller
{
    public function verbs(): array
    {
        return [
            "info" => ["GET"]
        ];
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionInfo(): array
    {
        $userLogged = new UserLoggedForm();
        $userLogged->load(Yii::$app->user->identity->getAttributes(), "");
        $role = array_key_first(Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
        return ResponseBuilder::responseJson(true, ["user" => array_merge($userLogged->getAttributes([
            "id", "username", "email", "logged_at", "created_at"
        ]), [
            "role" => $role
        ])]);
    }
}
