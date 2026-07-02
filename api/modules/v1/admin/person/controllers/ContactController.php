<?php

namespace api\modules\v1\admin\person\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use common\models\User;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\person\models\Contact;
use api\modules\v1\admin\person\models\search\ContactSearch;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class ContactController extends Controller
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => [User::ROLE_STAFF]
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR],
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $contact = new Contact();
        $contact->load(Yii::$app->request->post());
        if (!$contact->validate() || !$contact->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $contact->getErrors()], "Can't create Contact");
        }
        return ResponseBuilder::responseJson(true, compact("contact"), "Create Contact successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $contact = $this->findModel($id);
        $contact->load(Yii::$app->request->post());
        if (!$contact->validate() || !$contact->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $contact->getErrors()], "Can't update Contact");
        }
        return ResponseBuilder::responseJson(true, compact("contact"), "Update Contact successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $contact = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("contact"));
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $contact = $this->findModel($id);
        if ($contact->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Contact successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Contact");
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new ContactSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $contact = Contact::find()->where(["id" => $id])->unDelete()->one();
        if ($contact) {
            return $contact;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Contact not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
