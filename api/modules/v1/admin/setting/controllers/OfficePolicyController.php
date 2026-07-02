<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\web\HttpException;
use yii\rest\Controller;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\form\OfficePolicyForm;
use api\modules\v1\admin\setting\models\search\OfficePolicySearch;

class OfficePolicyController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
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
     * @throws yii\web\HttpException
     */
    public function actionCreate(): array
    {
        $officePolicy = new OfficePolicyForm();
        $officePolicy->load(Yii::$app->request->post());
        if (!$officePolicy->validate() || !$officePolicy->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $officePolicy->getErrors()], Yii::t("api", "Can't create Office Policy"));
        }
        return ResponseBuilder::responseJson(true, ["office_policy" => $officePolicy], "Create Office Policy success");
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $officePolicy = $this->findModel($id);
        $officePolicy->load(Yii::$app->request->post());
        if (!$officePolicy->validate() || !$officePolicy->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $officePolicy->getErrors()], Yii::t("api", "Can't update Office Policy"));
        }
        return ResponseBuilder::responseJson(true, ["office_policy" => $officePolicy], "Update Office Policy success");
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $officePolicy = $this->findModel($id);
        if ($officePolicy->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Office Policy success");
        }
        return ResponseBuilder::responseJson(false, null, "Can't Delete Office Policy");
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new OfficePolicySearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionView($id): array
    {
        $officePolicy = $this->findModel($id);
        return ResponseBuilder::responseJson(true, ["office_policy" => $officePolicy]);
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id): OfficePolicyForm
    {
        if ($officePolicy = OfficePolicyForm::find()->where(["id" => $id])->unDelete()->one()) {
            return $officePolicy;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Office Policy not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
