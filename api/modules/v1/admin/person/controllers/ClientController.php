<?php

namespace api\modules\v1\admin\person\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use api\modules\v1\admin\person\models\search\ClientSearch;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\person\models\Client;
use api\modules\v1\admin\person\models\form\CreateClientForm;

class ClientController extends Controller
{

    /**
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionCreate(): array
    {
        $client = new CreateClientForm();
        $client->load(Yii::$app->request->post());
        if (!$client->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $client->getErrors()], "Can't Create Client");
        }
        $client->saveClient();
        return ResponseBuilder::responseJson(true, compact("client"), "Create Client successfully");
    }

    /**
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new ClientSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionView(int $id): array
    {
        return ResponseBuilder::responseJson(true, ["client" => $this->findModel($id)]);
    }

    /**
     * @throws NotFoundHttpException
     * @author khuongdev2001
     */
    protected function findModel($id)
    {
        if (($model = Client::find()->where(["id" => $id])->active()->one()) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
