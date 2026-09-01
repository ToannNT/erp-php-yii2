<?php

namespace api\modules\v1\admin\product\controllers;

use api\modules\v1\admin\product\models\form\BrandForm;
use api\modules\v1\admin\product\models\form\SortForm;
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
     * Sắp xếp thứ tự hiển thị hàng loạt — FE kéo thả xong gửi 1 request duy nhất.
     *
     * Body: {"items": [{"id": 49, "priority": 1}, {"id": 52, "priority": 2}]}
     * Thiếu `priority` thì lấy vị trí trong mảng. Quy ước: priority nhỏ hiện trước.
     */
    public function actionSort(): array
    {
        $form = new SortForm(["modelClass" => Brand::class]);
        $form->load(Yii::$app->request->post());
        if (!$form->validate() || !$form->apply()) {
            return ResponseBuilder::responseJson(false, ["errors" => $form->getErrors()], "Can't sort Brand");
        }
        return ResponseBuilder::responseJson(true, ["applied" => $form->getApplied()], "Sort Brand successfully");
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
