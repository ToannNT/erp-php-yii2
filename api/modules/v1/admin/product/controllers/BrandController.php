<?php

namespace api\modules\v1\admin\product\controllers;

use api\modules\v1\admin\product\models\form\BrandForm;
use common\models\User;
use Yii;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\Brand;
use api\modules\v1\admin\product\models\search\BrandSearch;
use yii\rest\Controller;
use yii\web\HttpException;

class BrandController extends Controller
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['view', 'index'],
                    'roles' => [User::ROLE_STAFF, User::ROLE_SUPPLIER]
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
        $brand = new BrandForm();
        $brand->load(Yii::$app->request->post());
        if (!$brand->validate() || !$brand->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $brand->getErrors()], "Can't create Brand");
        }
        $brand->createOrDeleteCategory();
        return ResponseBuilder::responseJson(true, compact("brand"), "Create Brand successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id)
    {
        $brand = BrandForm::find()->where(["id" => $id])->unDelete()->one();
        if (!$brand) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Brand not found", ApiConstant::STATUS_NOT_FOUND);
            return $brand;
        }
        $brand->load(Yii::$app->request->post());
        if (!$brand->validate() || !$brand->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $brand->getErrors()], "Can't update Brand");
        }
        $brand->createOrDeleteCategory();
        return ResponseBuilder::responseJson(true, compact("brand"), "Update Brand successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $brand = $this->findModel($id);
        if ($brand->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Brand successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Delete Brand successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $brand = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("brand"));
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new BrandSearch())->search(Yii::$app->request->queryParams));
    }


    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $brand = Brand::find()->where(["id" => $id])->unDelete()->one();
        if (!$brand) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Brand not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return $brand;
    }
}
