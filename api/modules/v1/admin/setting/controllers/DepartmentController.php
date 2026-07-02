<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\web\HttpException;
use yii\rest\Controller;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\search\DepartmentSearch;
use api\modules\v1\admin\setting\models\form\DepartmentForm;

class DepartmentController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update'],
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
     * @return array
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $department = new DepartmentForm();

        $department->load(Yii::$app->request->post());

        if (!$department->validate() || !$department->save()) {

            return ResponseBuilder::responseJson(false, ["errors" => $department->getErrors()], "Can't Create Department");
        }
        return ResponseBuilder::responseJson(true, compact("department"), "Create Department successfully");
    }


    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $department = $this->findModel($id);

        $department->load(Yii::$app->request->post());

        if (!$department->validate() || !$department->save()) {

            return ResponseBuilder::responseJson(false, ["errors" => $department->getErrors()], "Can't Update Department");
        }
        return ResponseBuilder::responseJson(true, compact("department"), "Update Department successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete($id): array
    {
        $department = $this->findModel($id);

        if (!$department->softDelete()) {

            return ResponseBuilder::responseJson(false, null, "Can't Delete Department");
        }
        return ResponseBuilder::responseJson(true, null, "Delete Department successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $department = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("department"));
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new DepartmentSearch())->search(Yii::$app->request->queryParams));
    }


    /**
     * @throws HttpException
     */
    public function findModel($id): DepartmentForm
    {
        if ($department = DepartmentForm::findOne($id)) {
            return $department;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Department not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
