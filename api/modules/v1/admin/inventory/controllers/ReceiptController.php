<?php

namespace api\modules\v1\admin\inventory\controllers;

use api\modules\v1\admin\product\models\search\ProductInventorySearch;
use common\components\inventory\History;
use common\components\log\DbTarget;
use common\models\HistoryLog;
use common\models\InventoryReceipt as InventoryReceiptAlias;
use common\models\User;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\rest\Controller;
use yii\web\HttpException;
use common\models\InventoryHistory;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\inventory\models\form\ImportInventoryReceiptForm;
use api\modules\v1\admin\inventory\models\form\AddProductVariantForm;
use api\modules\v1\admin\inventory\models\form\InventoryReceiptForm;
use api\modules\v1\admin\inventory\models\form\InventoryReceiptItemForm;
use api\modules\v1\admin\inventory\models\InventoryReceipt;
use api\modules\v1\admin\inventory\models\InventoryReceiptItem;
use api\modules\v1\admin\inventory\models\ProductInventory;
use api\modules\v1\admin\inventory\models\search\InventoryReceiptSearch;

class ReceiptController extends Controller
{
    protected $errors;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["access_control_other"] = [
            'class' => AccessControl::class,
//            'only' => ['update', 'delete', 'approved', 'warehouse', 'done'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => [User::ROLE_SUPPLIER, User::ROLE_STAFF]
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
     * Define list middleware method in this controller
     */
    public function verbs(): array
    {
        return [
            "import" => ["POST"],
            "approved" => ["POST"],
            "warehouse" => ["POST"],
            "cancel" => ["POST"],
            "done" => ["POST"],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionImport(): array
    {
        $model = new ImportInventoryReceiptForm([
            "file" => UploadedFile::getInstanceByName("file")
        ]);
        try {
            if (!$model->validate()) {
                $this->setErrors("inventory_receipt", $model->getErrorSummary(true));
            }
            if (!is_dir(ImportInventoryReceiptForm::DIR_IMPORT)) {
                mkdir(ImportInventoryReceiptForm::DIR_IMPORT, 0644, true);
            }
            $filename = $model->getFilename();
            $model->file->saveAs($filename);
            return ResponseBuilder::responseJson(true, ["inventory_receipt" => $this->genarateProductByExcel($filename)]);
        } catch (Exception $e) {
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors, "message" => $e->getMessage()], "Can't get variants");
        }
    }

    public function actionDownloadTemplateImport()
    {
        $filename = "file/templates/template_import_inventory_receipt.xlsx";
        return Yii::$app->response->sendFile($filename, "template_import_inventory_receipt.xlsx");
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws Exception
     */
    private function genarateProductByExcel(string $filename): array
    {
        $errors = [];
        $variants = [];
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            if (!strval($sheet->getCellByColumnAndRow(1, $row))) {
                continue;
            }
            $receiptItem = [];
            foreach (ImportInventoryReceiptForm::MAP_CELL_EXCEL as $cellIndex => $cellValue) {
                $receiptItem[$cellValue] = strval($sheet->getCellByColumnAndRow($cellIndex, $row));
            }
            $variant = new AddProductVariantForm($receiptItem);
            if (!$variant->validate()) {
                $errors[] = $variant->buildTemplateError($row);
            }
            $variants[] = $variant->getAttributes();
        }
        if ($errors) {
            $this->setErrors("inventory_receipt", $errors);
        }
        return $variants;
    }

    /**
     * @throws HttpException
     * Status default 0, status in:0, 1
     * Method use JsonBehavior batch json field billing_address,...
     */
    public function actionCreate(): array
    {
        $receipt = new InventoryReceiptForm();
        $receipt->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        $task = "Create Inventory Receipt";
        try {
            if (!$receipt->validate() || !$receipt->save()) {
                $this->setErrors("receipt", $receipt->getErrorSummary(true));
            }
            $this->generateReceiptItem($receipt);
            $receipt->addProgressStatus(InventoryReceiptAlias::RECEIPT_STATUS_ORDER);
            if ($receipt->status == InventoryReceiptAlias::RECEIPT_STATUS_DONE) {
                $task = "Done Inventory Receipt";
                foreach ($receipt->inventoryReceiptItems as $item) {
                    $productInventory = ProductInventory::interactive($receipt, $item)
                        ->addAvailable($item->quantity)
                        ->addQuantity($item->quantity);
                    $productInventory->save(false);
                    InventoryHistory::create([
                        "action" => InventoryHistory::ACTION_INVENTORY_RECEIPT,
                        "change_quantity" => "+ $item->quantity",
                        "inventory" => $productInventory->available,
                        "voucher_code" => $receipt->code,
                        "created_by" => $receipt->created_by,
                        "inventory_id" => $receipt->inventory_id,
                        "office_id" => $receipt->office_id,
                        "product_id" => $item->product_id,
                        "product_variant_id" => $item->product_variant_id,
                        "type" => InventoryHistory::TYPE_INVENTORY_RECEIPT
                    ]);
                }
                $receipt->setStatus(InventoryReceipt::RECEIPT_STATUS_DONE);
                $receipt->addProgressStatus(InventoryReceipt::RECEIPT_STATUS_APPROVAL);
                $receipt->addProgressStatus(InventoryReceipt::RECEIPT_STATUS_DONE);
            }
            $receipt->save(false);
            Yii::$app->build_log->push($task, __METHOD__, DbTarget::TAG_CREATED, $receipt->getAttributes(), $receipt->getOldAttributes());
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("receipt"), "Receipt Inventory successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors]);
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException|Throwable
     */
    public function actionUpdate(int $id): array
    {
        $receipt = InventoryReceiptForm::find()->andWhere(["id" => $id])
            ->notDone()
            ->notWarehouse()
            ->withoutCancel()
            ->one();
        if (!$receipt) {
            return ResponseBuilder::responseJson(false, null, "Inventory Receipt not found");
        }
        $receipt->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$receipt->validate()) {
                $this->setErrors("receipt", $receipt->getErrorSummary(true));
            }
            if ($receipt->receipt_items) {
                $this->clearReceiptItem($receipt->id);
                $this->generateReceiptItem($receipt);
            }
            $receipt->save();
            Yii::$app->build_log->push("Update Inventory receipt", __METHOD__, DbTarget::TAG_UPDATED, $receipt->getAttributes(), $receipt->getOldAttributes());
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("receipt"), "Update Inventory Receipt");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't update Inventory Receipt");
        }
    }

    /**
     * @param $receipt_id
     * @return void
     */
    protected function clearReceiptItem($receipt_id): void
    {
        InventoryReceiptItem::deleteAll(["receipt_id" => $receipt_id]);
    }

    /**
     * @param InventoryReceiptForm $receipt
     * @return void
     * @throws Exception
     */
    protected function generateReceiptItem(InventoryReceiptForm $receipt)
    {
        foreach ($receipt->receipt_items as $item) {
            $receiptItem = new InventoryReceiptItemForm([
                "receipt_id" => $receipt->id
            ]);
            $receiptItem->receipt = $receipt;
            $receiptItem->load($item);
            if (!$receiptItem->validate() || !$receiptItem->save()) {
                $this->setErrors("receipt_item", $receiptItem->getErrorSummary(true));
            }
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionDone(int $id): array
    {
        $receipt = $this->findModel($id);
        if ($receipt->status != InventoryReceipt::RECEIPT_STATUS_ORDER) {
            return ResponseBuilder::responseJson(false, null, "Inventory Receipt allow only status warehouse");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($receipt->inventoryReceiptItems as $item) {
                $productInventory = ProductInventory::interactive($receipt, $item)
                    ->addAvailable($item->quantity)
                    ->addQuantity($item->quantity);
                $productInventory->save(false);
                InventoryHistory::create([
                    "action" => InventoryHistory::ACTION_INVENTORY_RECEIPT,
                    "change_quantity" => "+ $item->quantity",
                    "inventory" => $productInventory->available,
                    "voucher_code" => $receipt->code,
                    "created_by" => $receipt->created_by,
                    "inventory_id" => $receipt->inventory_id,
                    "office_id" => $receipt->office_id,
                    "product_id" => $item->product_id,
                    "product_variant_id" => $item->product_variant_id,
                    "type" => InventoryHistory::TYPE_INVENTORY_RECEIPT
                ]);
            }
            $receipt->setStatus(InventoryReceipt::RECEIPT_STATUS_DONE);
            $receipt->addProgressStatus(InventoryReceipt::RECEIPT_STATUS_APPROVAL);
            $receipt->addProgressStatus(InventoryReceipt::RECEIPT_STATUS_DONE);
            if (!$receipt->save(false)) {
                $this->setErrors("inventory_receipt", $receipt->getErrorSummary(true));
            }
            Yii::$app->build_log->push("Done Inventory receipt", __METHOD__, DbTarget::TAG_UPDATED, $receipt->getAttributes(), $receipt->getOldAttributes());
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't change status done");
        }
        return ResponseBuilder::responseJson(true, compact("receipt"), "Change status Done successfully");
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionCancel(int $id): array
    {
        $receipt = InventoryReceipt::find()
            ->andWhere(["id" => $id])
            ->withoutCancel()
            ->one();
        if (!$receipt) {
            return ResponseBuilder::responseJson(false, null, "Inventory Receipt not found");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($receipt->status == InventoryReceipt::RECEIPT_STATUS_WAREHOUSE) {
                foreach ($receipt->inventoryReceiptItems as $item) {
                    ProductInventory::interactive($receipt, $item)
                        ->addIncoming(-$item->quantity)
                        ->save(false);
                }
            } else if ($receipt->status == InventoryReceipt::RECEIPT_STATUS_DONE) {
                foreach ($receipt->inventoryReceiptItems as $item) {
                    $productInventory = ProductInventory::interactive($receipt, $item)
                        ->addAvailable(-$item->quantity)
                        ->addQuantity(-$item->quantity);
                    $productInventory->save(false);
                    InventoryHistory::create([
                        "action" => InventoryHistory::ACTION_CANCEL_INVENTORY_RECEIPT,
                        "change_quantity" => "- $item->quantity",
                        "inventory" => $productInventory->available,
                        "voucher_code" => $receipt->code,
                        "created_by" => $receipt->created_by,
                        "inventory_id" => $receipt->inventory_id,
                        "product_id" => $item->product_id,
                        "product_variant_id" => $item->product_variant_id
                    ]);
                }
            }
            $receipt->setStatus(InventoryReceipt::RECEIPT_STATUS_CANCEL);
            $receipt->addProgressStatus(InventoryReceipt::RECEIPT_STATUS_CANCEL);
            if (!$receipt->save(false)) {
                $this->setErrors("inventory_receipt", $receipt->getErrorSummary(true));
            }
            Yii::$app->build_log->push("Cancel Inventory receipt", __METHOD__, DbTarget::TAG_UPDATED, $receipt->getAttributes(), $receipt->getOldAttributes());
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("receipt"), "Change Inventory Receipt successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors], "Can't Cancel Inventory Receipt");
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $receipt = InventoryReceipt::find()
            ->andWhere(["id" => $id])
            ->one();
        if (!$receipt) {
            return ResponseBuilder::responseJson(false, null, "Inventory Receipt not found");
        }
        return ResponseBuilder::responseJson(true, compact("receipt"));
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new InventoryReceiptSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $model = InventoryReceipt::find()->where(["id" => $id])->withoutCancel()->one();
        if ($model) {
            return $model;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Receipt not found", ApiConstant::STATUS_NOT_FOUND);
    }

    /**
     * @throws Exception
     */
    protected function setErrors($key, $errors)
    {
        $this->errors = [
            $key => is_string($errors) ? [$errors] : $errors
        ];
        throw new  Exception(json_encode($errors));
    }

    public function actionExportOverview()
    {
        $dataProvider = (new InventoryReceiptSearch())->search(Yii::$app->request->queryParams);
        $inventoryReceipts = $dataProvider->query->orderBy(["inventory_receipt.id" => SORT_DESC])->all();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getSheet(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle("A1:K1")->getFont()->setBold(true);
        $numRow = 1;
        $numColumn = 1;
        $headers = ["Stt", "Mã Nhập Kho", "Chi Nhánh", "Kho", "Nhà Cung Cấp", "Tạo Bởi", "Tổng Số", "Ngày Tạo", "Ngày Hoàn Thành", "Trạng Thái"];
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($numColumn, 1, $header);
            $numColumn++;
        }
        $numColumn = 1;
        $serial = 0;
        /**
         * @var InventoryReceiptAlias $inventoryReceipt
         */
        foreach ($inventoryReceipts as $inventoryReceipt) {
            $numRow++;
            $serial++;
            $sheet->setCellValueByColumnAndRow(1, $numRow, $serial);
            $sheet->setCellValueByColumnAndRow(2, $numRow, $inventoryReceipt->code);
            $sheet->setCellValueByColumnAndRow(3, $numRow, $inventoryReceipt->office->name);
            $sheet->setCellValueByColumnAndRow(4, $numRow, $inventoryReceipt->inventory->name);
            $sheet->setCellValueByColumnAndRow(5, $numRow, $inventoryReceipt->supplier->name);
            $sheet->setCellValueByColumnAndRow(6, $numRow, $inventoryReceipt->createdBy->username);
            $sheet->setCellValueByColumnAndRow(7, $numRow, $inventoryReceipt->quantity);
            $sheet->setCellValueByColumnAndRow(8, $numRow, $inventoryReceipt->created_at);
            $sheet->setCellValueByColumnAndRow(9, $numRow, $inventoryReceipt->delivery_date);
            $sheet->setCellValueByColumnAndRow(10, $numRow, $inventoryReceipt->getStatus());
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if (!is_dir("file/exports")) {
            mkdir("file/exports", 0700, true);
        }
        $filename = "file/exports/export_inventory_receipt_overview.xlsx";
        $writer->save($filename);
        return Yii::$app->response->sendFile($filename, "export_inventory_receipt_overview.xlsx");
    }
}
