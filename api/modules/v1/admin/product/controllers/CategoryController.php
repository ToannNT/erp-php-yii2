<?php

namespace api\modules\v1\admin\product\controllers;

use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\Category;
use api\modules\v1\admin\product\models\search\CategorySearch;

class CategoryController extends Controller
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
        $category = new Category();
        $category->load(Yii::$app->request->post());
        if (!$category->validate() || !$category->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Can't create Category");
        }
        return ResponseBuilder::responseJson(true, compact("category"), "Create Category successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $category = $this->findModel($id);
        $category->load(Yii::$app->request->post());
        if (!$category->validate() || !$category->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Can't update Category");
        }
        return ResponseBuilder::responseJson(true, compact("category"), "Update Category successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $category = $this->findModel($id);
        if ($category->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Category successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Category successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $category = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("category"));
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new CategorySearch())->search(Yii::$app->request->queryParams));
    }

    public function findModel(int $id)
    {
        $category = Category::find()->where(["id" => $id])->unDelete()->one();
        if ($category) {
            return $category;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Category not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
