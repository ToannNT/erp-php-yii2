<?php

namespace api\modules\v1\frontend\user\controllers;

use Yii;
use api\helper\response\{ApiConstant, ResponseBuilder};
use api\modules\v1\frontend\user\models\{
    form\SaveForm,
    form\LoginForm,
    User
};

class SiteController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\CompositeAuth::class,
            'only' => ['update','info'],
            'authMethods' => [
                \yii\filters\auth\HttpBearerAuth::class
            ],
        ];
        return $behaviors;
    }

    /**
     * @description here is action login
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionLogin(): array
    {
        $request = Yii::$app->request;
        $email = $request->post("email", " ");
        $password = $request->post("password", " ");
        $user = LoginForm::findByEmailOrUserName($email);
        if (!$user || !$user->validatePassword($password)) {
            return ResponseBuilder::responseJson(false, null, Yii::t("api", "Invalid Username or Password"), ApiConstant::STATUS_UNAUTHORIZED);
        }
        $user->generateToken();
        $user->logged_at = date("Y-m-d h:i:s");
        $user->save(false);
        return ResponseBuilder::responseJson(true, compact("user"), Yii::t("api", "Login successfully"));
    }

    /**
     * @description here is action register
     * @return array
     * @throws yii\base\Exception
     * @throws yii\base\InvalidConfigException
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionSignup(): array
    {
        $request = Yii::$app->request;
        $user = new SaveForm();
        $user->setScenario("update");
        $user->load($request->post(), "");
        if (!$user->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $user->getErrors()
            ]);
        }
        $user->setPassword($user->password);
        $user->generateAccessToken();
        $user->generateAuthKey();
        $user->save();
        /* Here Can Call Mailer*/
        return ResponseBuilder::responseJson(
            true,
            compact("user"),
            Yii::t("api", "Register successfully please check email")
        );
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionCheckActive($access_token): array
    {
        $user = $this->findModelByAccessToken($access_token);
        return ResponseBuilder::responseJson(true, compact("user"));
    }

    /**
     * @throws Yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionForgot($access_token): array
    {
        $request = Yii::$app->request;
        $user = $this->findModelByAccessToken($access_token);
        $user->load($request->post(), "");
        if (!$user->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $user->getErrors()
            ]);
        }
        $user->setPassword($request->post("password"));
        $user->save(false);
        return ResponseBuilder::responseJson(true, null, Yii::t("api", "Forgot user success"));
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    private function findModelByAccessToken($access_token)
    {
        $user = SaveForm::findByAccessToken($access_token);
        if ($user) {
            return $user;
        }
        return ResponseBuilder::responseJson(false, null, "", ApiConstant::STATUS_NOT_FOUND);
    }

    /**
     * @throws Yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionActive($access_token): array
    {
        $user = $this->findModelByAccessToken($access_token);
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);
        return ResponseBuilder::responseJson(true, compact("user"), Yii::t("api", "Update status successfully"));
    }


    /**
     * @throws \yii\web\HttpException
     */
    public function actionInfo()
    {
        return ResponseBuilder::responseJson(true, [
            "user" => Yii::$app->user->identity
        ]);
    }

    /**
     * @return array $response {}
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionView($id): array
    {
        $user = User::findOne(["id" => $id]);
        if ($user) {
            return ResponseBuilder::responseJson(true, compact("user"));
        }
        return ResponseBuilder::responseJson(false, null, "User not found by id");
    }

    /**
     *
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionUpdate(): array
    {
        $request = Yii::$app->request;
        $user = SaveForm::find()->active()
            ->andWhere(["id" => Yii::$app->user->identity->getId()])->one();
        $user->load($request->post(), "");
        if (!$user->validate()) {
            return ResponseBuilder::responseJson(false, [
                "errors" => $user->getErrors()
            ]);
        }
        return ResponseBuilder::responseJson(true, compact("user"));
    }
}
