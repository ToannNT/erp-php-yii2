<?php

namespace api\modules\v1\admin\inventory\controllers;

use api\modules\v1\admin\inventory\models\InventoryReceipt;
use api\modules\v1\admin\inventory\models\search\InventoryReceiptSearch;
use common\components\log\DbTarget;
use common\models\InventoryReceipt as InventoryReceiptAlias;
use common\models\User;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\rest\Controller;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\inventory\models\form\StocktakingForm;
use api\modules\v1\admin\inventory\models\form\StocktakingItemForm;
use api\modules\v1\admin\inventory\models\ProductInventory;
use api\modules\v1\admin\inventory\models\search\StocktakingSearch;
use api\modules\v1\admin\inventory\models\Stocktaking;
use api\modules\v1\admin\inventory\models\StocktakingItem;
use common\models\InventoryHistory;

class StocktakingController extends Controller
{
    protected $errors;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["access_control_other"] = [
            'class' => AccessControl::class,
//            'only' => ['create', 'update', 'view', 'index', 'balance', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => [User::ROLE_STAFF]
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR]
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $stocktaking = new StocktakingForm();
        $stocktaking->setScenario(Stocktaking::SCENARIO_CREATE);
        $stocktaking->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$stocktaking->validate() || !$stocktaking->save()) {
                $this->setErrors("stocking", $stocktaking->getErrors());
            }
            $stocktaking->total_difference = 0;
            $stocktaking->total_adjustment = 0;
            foreach ($stocktaking->stocktaking_items as $item) {
                $this->generateStocktakingItem($stocktaking, $item);
                $stocktaking->total_difference += $item["number_difference"];
                $stocktaking->total_adjustment += $item["number_adjustment"];
            }
            $stocktaking->setFormatCode();
            $stocktaking->addProgressStatus(Stocktaking::STATUS_PENDING);
            $stocktaking->save(false);
            Yii::$app->build_log->push("Create Stocktaking", __METHOD__, DbTarget::TAG_CREATED, $stocktaking->getAttributes(), $stocktaking->getOldAttributes());
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["stocktaking" => $stocktaking], "Create Stocktaking successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors]);
        }
    }


    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $stocktaking = StocktakingForm::find()
            ->where(["id" => $id])
            ->notCancel()
            ->notDone()
            ->one();
        if (!$stocktaking) {
            return ResponseBuilder::responseJson(false, null, "Stocktaking not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $stocktaking->load(Yii::$app->request->post());
        try {
            if (!$stocktaking->validate() || !$stocktaking->save()) {
                $this->setErrors("stocktaking", $stocktaking->getErrors());
            }
            if ($stocktaking->stocktaking_items) {
                $this->clearStocktakingItem($stocktaking);
                $stocktaking->total_difference = 0;
                $stocktaking->total_adjustment = 0;
                foreach ($stocktaking->stocktaking_items as $item) {
                    $this->generateStocktakingItem($stocktaking, $item);
                    $stocktaking->total_difference += $item["number_difference"];
                    $stocktaking->total_adjustment += $item["number_adjustment"];
                }
            }
            Yii::$app->build_log->push("Update Stocktaking", __METHOD__, DbTarget::TAG_UPDATED, $stocktaking->getAttributes(), $stocktaking->getOldAttributes());
            $stocktaking->save(false);
            return ResponseBuilder::responseJson(true, compact("stocktaking"), "Update Stocktaking successfully");
        } catch (Exception $e) {
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't Update Stocktaking");
        }
    }

    /**
     * @param $stocktaking
     * @param $item
     * @return void
     * @throws Exception
     */
    protected function generateStocktakingItem($stocktaking, $item)
    {
        $stocktakingItem = new StocktakingItemForm();
        $stocktakingItem->stocktaking = $stocktaking;
        $stocktakingItem->stocktaking_id = $stocktaking->id;
        $stocktakingItem->load($item);
        if (!$stocktakingItem->validate() || !$stocktakingItem->save()) {
            $this->setErrors("stocktaking_item", $stocktakingItem->getErrorSummary(true));
        }
    }

    /**
     * @param $stocktaking
     * @return void
     */
    protected function clearStocktakingItem($stocktaking)
    {
        StocktakingItem::deleteAll(["stocktaking_id" => $stocktaking->id]);
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new StocktakingSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $stocktaking = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("stocktaking"));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionBalance(int $id): array
    {
        $stocktaking = StocktakingForm::find()
            ->where(["id" => $id])
            ->notCancel()
            ->notDone()
            ->one();
        if (!$stocktaking) {
            return ResponseBuilder::responseJson(false, null, "Stocktaking not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($stocktaking->stocktakingItems as $item) {
                $productInventory = ProductInventory::interactive($stocktaking, $item)
                    ->setQuantity($item->number_adjustment)
                    ->setAvailable($item->number_adjustment);
                if (!$productInventory->save(false)) {
                    $this->setErrors("stocking_item", $productInventory->getErrors());
                }
                InventoryHistory::create([
                    "action" => InventoryHistory::ACTION_BALANCE,
                    "change_quantity" => $item->number_difference,
                    "inventory" => $productInventory->available,
                    "voucher_code" => $stocktaking->code,
                    "created_by" => $stocktaking->created_by,
                    "inventory_id" => $stocktaking->inventory_id,
                    "product_id" => $item->product_id,
                    "product_variant_id" => $item->product_variant_id,
                    "type" => InventoryHistory::TYPE_INVENTORY_STOCKTAKING
                ]);
            }
            $stocktaking->stocktaking_date = date("Y-m-d H:i:s");
            $stocktaking->setStatus(Stocktaking::STATUS_DONE);
            $stocktaking->addProgressStatus(Stocktaking::STATUS_DONE);
            if (!$stocktaking->save(false)) {
                $this->setErrors("stocktaking", $stocktaking->getErrors());
            }
            Yii::$app->build_log->push("Balance Stocktaking", __METHOD__, DbTarget::TAG_UPDATED, $stocktaking->getAttributes(), $stocktaking->getOldAttributes());
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("stocktaking"), "Balance Stocktaking successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't Balance Stocktaking");
        }
    }

    /**
     * @throws Exception
     */
    protected function setErrors($key, $errors)
    {
        $this->errors = [
            $key => $errors
        ];
        throw new  Exception(json_encode($errors));
    }

    /**
     * @throws HttpException
     */
    public function actionCancel(int $id)
    {
        $stocktaking = Stocktaking::find()->where(["id" => $id])->pedding()->one();
        if (!$stocktaking) {
            return ResponseBuilder::responseJson(false, null, "Stocktaking not found");
        }
        $stocktaking->setStatus(Stocktaking::STATUS_CANCEL);
        $stocktaking->addProgressStatus(Stocktaking::STATUS_CANCEL);
        if ($stocktaking->save(false)) {
            Yii::$app->build_log->push("Cancel Stocktaking", __METHOD__, DbTarget::TAG_UPDATED, $stocktaking->getAttributes(), $stocktaking->getOldAttributes());
            return ResponseBuilder::responseJson(true, null, "Change status successfully");
        }
    }

    /**
     * @param int $id
     * @return array|\common\models\Stocktaking
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $stocktaking = Stocktaking::find()->where(["id" => $id])->one();
        if ($stocktaking) {
            return $stocktaking;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Stocktaking not found", ApiConstant::STATUS_NOT_FOUND);
    }

    public function actionExportOverview()
    {
        $dataProvider = (new StocktakingSearch())->search(Yii::$app->request->queryParams);
        $stocktakings = $dataProvider->query->orderBy(["stocktaking.id" => SORT_DESC])->all();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getSheet(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle("A1:I1")->getFont()->setBold(true);
        $numRow = 1;
        $numColumn = 1;
        $headers = ["Stt", "Mã Kiểm Kê", "Chi Nhánh", "Kho", "Ghi Chú", "Tạo Bởi", "Ngày Tạo", "Ngày Kiểm Kê", "Trạng Thái"];
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($numColumn, 1, $header);
            $numColumn++;
        }
        $numColumn = 1;
        $serial = 0;
        /**
         * @var \common\models\Stocktaking $stocktaking
         */
        foreach ($stocktakings as $stocktaking) {
            $numRow++;
            $serial++;
            $sheet->setCellValueByColumnAndRow(1, $numRow, $serial);
            $sheet->setCellValueByColumnAndRow(2, $numRow, $stocktaking->code);
            $sheet->setCellValueByColumnAndRow(3, $numRow, $stocktaking->office->name);
            $sheet->setCellValueByColumnAndRow(4, $numRow, $stocktaking->inventory->name);
            $sheet->setCellValueByColumnAndRow(5, $numRow, $stocktaking->note);
            $sheet->setCellValueByColumnAndRow(6, $numRow, $stocktaking->createdBy->username);
            $sheet->setCellValueByColumnAndRow(8, $numRow, $stocktaking->created_at);
            $sheet->setCellValueByColumnAndRow(8, $numRow, $stocktaking->stocktaking_date);
            $sheet->setCellValueByColumnAndRow(7, $numRow, $stocktaking->getStatus());
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if (!is_dir("file/exports")) {
            mkdir("file/exports", 0700, "true");
        }
        $filename = "file/exports/export_stocktaking_overview.xlsx";
        $writer->save($filename);
        return Yii::$app->response->sendFile($filename, "export_stocktaking_overview.xlsx");
    }
}
