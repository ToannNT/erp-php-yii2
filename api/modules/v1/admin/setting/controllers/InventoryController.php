<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\form\InventoryForm;
use api\modules\v1\admin\setting\models\Inventory;
use api\modules\v1\admin\setting\models\search\InventorySearch;

class InventoryController extends Controller
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
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $inventory = new InventoryForm();
        $inventory->load(Yii::$app->request->post());
        if (!$inventory->validate()  || !$inventory->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $inventory->getErrors()], "Can't create Inventory");
        }
        return ResponseBuilder::responseJson(true, compact("inventory"), "Create Inventory successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $inventory = $this->findModel($id);
        $inventory->load(Yii::$app->request->post());
        if (!$inventory->validate()  || !$inventory->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $inventory->getErrors()], "Can't update Inventory");
        }
        return ResponseBuilder::responseJson(true, compact("inventory"), "Update Inventory successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $inventory = $this->findModel($id);
        if ($inventory->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Inventory successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Inventory");
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new InventorySearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $inventory = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("inventory"));
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $inventory = Inventory::find()->unDelete()->andWhere(compact("id"))->one();
        if ($inventory) {
            return $inventory;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Inventory not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
