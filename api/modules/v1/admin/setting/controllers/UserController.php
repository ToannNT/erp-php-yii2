<?php

namespace api\modules\v1\admin\setting\controllers;

use Exception;
use Yii;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use common\models\UserSupplier;
use common\models\UserOffice;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\search\UserSearch;
use api\modules\v1\admin\setting\models\User;

class UserController extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['administrator', 'manager'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['administrator'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_CREATE);
        $user->load(Yii::$app->request->post());
        if (!$user->validate()) {
            $errors = $user->getFirstErrors();
            return ResponseBuilder::responseJson(false, compact("errors"), current($errors));
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user->generateAuthKey();
            $user->generateAccessToken();
            $user->save();
            $this->assignRoleToUser($user);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $user->getErrors(), "message" => $e->getMessage()], "Can't create User");
        }
        return ResponseBuilder::responseJson(true, compact("user"), "Create User successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $user = $this->findModel($id);
        $user->load(Yii::$app->request->post());
        if (!$user->validate()) {
            $errors = $user->getFirstErrors();
            return ResponseBuilder::responseJson(false, compact("errors"), current($errors));
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user->save();
            $this->assignRoleToUser($user);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $user->getErrors()], "Can't update User");
        }
        return ResponseBuilder::responseJson(true, compact("user"), "Create Update successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $user = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("user"));
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new UserSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws Exception
     */
    protected function assignRoleToUser(User $user)
    {
        switch ($user->role) {
            case User::ROLE_SELLER:
            case User::ROLE_MANAGER:
                $this->initUserOffice($user->offices, $user->getId());
                break;
            case User::ROLE_SUPPLIER;
                $this->initUserSuppliers($user->suppliers, $user->getId());
                break;
        }
    }


    /**
     * @throws Exception
     */
    protected function initUserOffice($offices, $user_id)
    {
        $this->revokeUserOffice($user_id);
        foreach ($offices as $id) {
            $userOffice = new UserOffice();
            $userOffice->user_id = $user_id;
            $userOffice->office_id = $id;
            if (!$userOffice->save(false)) {
                throw new Exception;
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function initUserSuppliers($suppliers, $user_id)
    {
        $this->revokeUserSupplier($user_id);
        foreach ($suppliers as $id) {
            $userSupplier = new UserSupplier();
            $userSupplier->user_id = $user_id;
            $userSupplier->supplier_id = $id;
            if (!$userSupplier->save(false)) {
                throw new Exception;
            }
        }
    }

    protected function revokeUserOffice($user_id)
    {
        UserOffice::deleteAll(["user_id" => $user_id]);
    }

    protected function revokeUserSupplier($user_id)
    {
        UserSupplier::deleteAll(["user_id" => $user_id]);
    }

    public function actionRole(): array
    {
        return ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name');
    }

    public function actionDelete(int $id)
    {
        $user = $this->findModel($id);
        if ($user->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete User successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't Delete User");
    }

    /**
     * @throws HttpException
     */
    public function findModel($id): User
    {
        if ($user = User::find()->where(compact("id"))->notDelete()->one()) {
            return $user;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "User not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
