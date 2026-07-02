<?php

namespace api\modules\v1\admin\setting\controllers;

use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use common\models\Inventory;
use api\modules\v1\admin\setting\models\search\OfficeSearch;
use api\modules\v1\admin\setting\models\form\OfficeForm;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;

class OfficeController extends Controller
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
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new OfficeSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $office = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("office"));
    }

    /**
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionCreate(): array
    {
        $office = new OfficeForm();
        $office->load(Yii::$app->request->post());
        if (!$office->validate() || !$office->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $office->getErrors()], Yii::t("api", "Can't create Office"));
        }
        return ResponseBuilder::responseJson(true, compact("office"), Yii::t("api", "Create Office successfully"));
    }

    /**
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionUpdate(int $id): array
    {
        $office = $this->findModel($id);
        $office->load(Yii::$app->request->post());
        if (!$office->validate() || !$office->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $office->getErrors()], Yii::t("api", "Can't create Office"));
        }
        return ResponseBuilder::responseJson(true, compact("office"), Yii::t("api", "Update Office successfully"));
    }

    /**
     * @throws HttpException
     * @author khuongdev2001
     */
    public function actionDelete($id): array
    {
        $office = $this->findModel($id);
        if ($office->softDelete()) {
            /**
             * @var Inventory $inventory
             */
            foreach ($office->inventories as $inventory) {
                $inventory->softDelete();
            }
            return ResponseBuilder::responseJson(true, null, Yii::t("api", "Delete Office successfully"));
        }
        return ResponseBuilder::responseJson(false, null, Yii::t("api", "Can't delete Office"));
    }

    /**
     * @throws HttpException
     */
    public function findModel($id): OfficeForm
    {
        if ($office = OfficeForm::findOne($id)) {
            return $office;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, Yii::t("api", "Office not found"), ApiConstant::STATUS_NOT_FOUND);
    }
}
