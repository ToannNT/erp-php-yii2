<?php

namespace api\modules\v1\admin\services\controllers;

use common\models\User;
use common\models\UserIdentity;
use common\models\UserToken;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\HttpException;
use yii\rest\Controller;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\services\models\Auth;

class AuthController extends Controller
{
    public function verbs(): array
    {
        return [
            "login" => ["POST"],
            "me" => ["GET"]
        ];
    }

    /**
     * @param $jwt
     * @return array
     * @throws GuzzleException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionLoginNewzen($jwt)
    {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => env("NEWZEN_BASE_URL"),
            // You can set any number of default request options.
            'timeout' => 10.0,
        ]);
        $response = $client->get("api/v1/public/project/site/exchange", [
            "query" => [
                "jwt" => $jwt
            ]
        ]);
        if ($response->getStatusCode() !== ApiConstant::STATUS_OK) {
            return ResponseBuilder::responseJson(false, [], "Can't login oauth", ApiConstant::STATUS_UNAUTHORIZED);
        }
        $body = json_decode($response->getBody()->getContents(), true);
        $payload = $body["data"]["payload"];
        // check tenant exist
        $tenantId = $payload["service"] . "_" . $payload["project_id"];
//        Yii::$app->helperDb->setTenant($tenantId)->initDatabase()->open();
        $user = UserIdentity::find()->where(["id" => User::USER_ID_ADMIN])->one();
        $user->generateToken();
        return ResponseBuilder::responseJson(true, [
            "tenant_id" => $tenantId,
            "token" => $user->token
        ], "Login Success");
    }

    public function actionLogin()
    {
        $auth = new Auth();
        $auth->load(Yii::$app->request->post(), "");
        if (!$auth->validate() || !($user = $auth->login())) {
            return ResponseBuilder::responseJson(false, ["errors" => $auth->getErrors()], "Can't login email or password invalid", ApiConstant::STATUS_UNAUTHORIZED);
        }
        return $user;
    }

    public function actionMe(): array
    {
        $user = Yii::$app->user->identity;
        return ResponseBuilder::responseJson(true, ["user" => Auth::findOne($user->getId())]);
    }
}
