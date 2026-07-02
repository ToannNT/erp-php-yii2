<?php

namespace api\modules\v1\admin\person\controllers;

use common\models\User;
use Yii;
use Exception;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\person\models\Contact;
use api\modules\v1\admin\person\models\form\CreateCustomerForm;
use api\modules\v1\admin\person\models\Customer;
use api\modules\v1\admin\person\models\search\CustomerSearch;
use common\models\ContactCustomer;
use common\models\Group;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
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
     * Lists all Customer models.
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new CustomerSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * Displays a single Customer model.
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        return ResponseBuilder::responseJson(true, ["customer" => $this->findModel($id)]);
    }

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return array
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $customer = new CreateCustomerForm();
        $customer->load(Yii::$app->request->post());
        if (!$customer->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $customer->getErrors()]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $contact = new Contact($customer->mapField($customer->attributeContacts()));
            $contact->save(false);
            $customer->owner_id = $contact->id;
            $groups = [];
            foreach ($customer->groups as $name) {
                $groups[] = (Group::createOrFind($name))->id;
            }
            $customer->groups = $groups;
            $customer->save();
            (new ContactCustomer(["customer_id" => $customer->id, "contact_id" => $contact->id]))->save(false);
            $transaction->commit();
            return ResponseBuilder::responseJson(true, null, "Create Customer successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $e->getLine()]);
        }
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return array
     * @throws NotFoundHttpException|HttpException
     */
    public function actionUpdate(int $id): array
    {
        $customer = $this->findModel($id);
        $customer->load(Yii::$app->request->post());
        if (!$customer->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $customer->getErrors()], "Can't Update Customer");
        }
        $groups = [];
        foreach ($customer->groups as $name) {
            $groups[] = (Group::createOrFind($name))->id;
        }
        $customer->groups = $groups;
        if ($customer->save()) {
            return ResponseBuilder::responseJson(true, compact("customer"), "Update Customer successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $customer->getErrors()], "Can't Update Customer");
    }

    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $this->findModel($id)->softDelete();

        return ResponseBuilder::responseJson(true, null, "Delete Customer successfully");
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Customer
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
