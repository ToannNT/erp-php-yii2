<?php

namespace api\modules\v1\frontend\pos\controllers;

use Yii;
use yii\web\HttpException;
use yii\rest\Controller;
use api\modules\v1\frontend\pos\models\search\CustomerSearch;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\Customer;
use api\modules\v1\frontend\pos\models\form\CustomerForm;

class CustomerController extends Controller
{
    /**
     * @return array
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        $model = new CustomerSearch();
        return ResponseBuilder::responseJson(true, $model->search(Yii::$app->request->queryParams));
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionView($id): array
    {
        $customer = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("customer"));
    }

    /**
     * @return array
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionCreate(): array
    {
        $customer = new CustomerForm();
        $customer->load(Yii::$app->request->post(), "");
        if (!$customer->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $customer->getErrors()]);
        }
        $customer->save();
        return ResponseBuilder::responseJson(true, $customer, "Add Customer successfully");
    }

    /**
     * @param integer $id
     * @return array|\common\models\Customer
     * @throws HttpException
     */
    protected function findModel(int $id)
    {
        $customer = Customer::find()->andWhere(["id" => $id])->one();
        if (!$customer) {
            throw new HttpException(
                ApiConstant::STATUS_NOT_FOUND,
                "Customer not found",
                ApiConstant::STATUS_NOT_FOUND
            );
        }
        return $customer;
    }
}
