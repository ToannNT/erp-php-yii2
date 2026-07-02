<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\modules\v1\admin\setting\models\form\SubDepartmentForm;
use api\modules\v1\admin\setting\models\search\SubDepartmentSearch;
use api\helper\response\ResponseBuilder;
use api\helper\response\ApiConstant;


class SubDepartmentController extends Controller
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
        $subDepartment = new SubDepartmentForm();
        $subDepartment->load(Yii::$app->request->post());
        if (!$subDepartment->validate() || !$subDepartment->save()) {

            return ResponseBuilder::responseJson(false, ["errors" => $subDepartment->getErrors()], "Can't Create Sub Department");
        }
        return ResponseBuilder::responseJson(true, ["sub_department" => $subDepartment], "Create Sub Department successfully");
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionUpdate($id): array
    {
        $subDepartment = $this->findModel($id);
        $subDepartment->load(Yii::$app->request->post());
        if (!$subDepartment->validate() || !$subDepartment->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $subDepartment->getErrors()], "Can't Create Sub Department");
        }
        return ResponseBuilder::responseJson(true, ["sub_department" => $subDepartment], "Update Sub Department successfully");
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionDelete($id): array
    {
        $subDepartment = $this->findModel($id);
        if ($subDepartment->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Sub Department successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't Delete Sub Department");
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new SubDepartmentSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $subDepartment = $this->findModel($id);
        return ResponseBuilder::responseJson(true, ["sub_department" => $subDepartment]);
    }


    /**
     * @throws HttpException
     */
    public function findModel($id)
    {
        $subDepartment = SubDepartmentForm::find()->where(["id" => $id])->unDelete()->one();
        if ($subDepartment) {
            return $subDepartment;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Sub Department not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
