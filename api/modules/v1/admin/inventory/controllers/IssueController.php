<?php

namespace api\modules\v1\admin\inventory\controllers;

use api\modules\v1\admin\inventory\models\InventoryReceipt;
use api\modules\v1\admin\inventory\models\search\InventoryReceiptSearch;
use api\modules\v1\admin\inventory\models\search\StocktakingSearch;
use common\models\User;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use api\trails\ErrorTrait;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\rest\Controller;
use common\models\InventoryHistory;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\inventory\models\form\InventoryIssueForm;
use api\modules\v1\admin\inventory\models\form\InventoryIssueItemForm;
use api\modules\v1\admin\inventory\models\InventoryIssue;
use api\modules\v1\admin\inventory\models\InventoryIssueItem;
use api\modules\v1\admin\inventory\models\ProductInventory;
use api\modules\v1\admin\inventory\models\search\InventoryIssueSearch;

class IssueController extends Controller
{
    use ErrorTrait;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["access_control_other"] = [
            'class' => AccessControl::class,
//            'only' => ['update', 'delivery', 'done', 'cancel', 'index', 'view'],
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
     * @return \string[][]
     */
    public function verbs(): array
    {
        return [
            "delivery" => ["POST"],
            "done" => ["POST"],
            "cancel" => ["POST"]
        ];
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $inventoryIssue = new InventoryIssueForm();
        $inventoryIssue->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$inventoryIssue->validate() || !$inventoryIssue->save(false)) {
                $this->setErrors("inventory_issue", $inventoryIssue->getErrorSummary(true));
            }
            $inventoryIssue->total_number = 0;
            foreach ($inventoryIssue->issue_items as $item) {
                $this->generateIssueItem($inventoryIssue, $item);
                $inventoryIssue->total_number += $item["quantity"];
            }
            $inventoryIssue->setFormatCode();
            $inventoryIssue->addProgressStatus(InventoryIssue::STATUS_PENDING);
            if (!$inventoryIssue->save(false)) {
                $this->setErrors("inventory_issue_item", $inventoryIssue->getErrorSummary(true));
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["inventory_issue" => $inventoryIssue], "Create Inventory Issue successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->getErrors()]);
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $inventoryIssue = InventoryIssueForm::find()->where(["id" => $id])
            ->notDone()
            ->notCancel()
            ->notDelivery()
            ->one();
        if (!$inventoryIssue) {
            return ResponseBuilder::responseJson(false, null, "Inventory Issue not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $inventoryIssue->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$inventoryIssue->validate()) {
                $this->setErrors("inventory_issue", $inventoryIssue->getErrorSummary(true));
            }
            $this->clearIssueItem($inventoryIssue);
            $inventoryIssue->total_number = 0;
            foreach ($inventoryIssue->issue_items as $item) {
                $this->generateIssueItem($inventoryIssue, $item);
                $inventoryIssue->total_number += $item["quantity"];
            }
            if (!$inventoryIssue->save(false)) {
                $this->setErrors("inventory_issue_item", $inventoryIssue->getErrorSummary(true));
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["inventory_issue" => $inventoryIssue], "Update Inventory successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->getErrors()], "Can't Update Inventory Issue");
        }
    }

    /**
     * @param $issue
     * @param $item
     * @return void
     * @throws Exception
     */
    protected function generateIssueItem($issue, $item)
    {
        $issueItem = new InventoryIssueItemForm();
        $issueItem->issue = $issue;
        $issueItem->inventory_issue_id = $issue->id;
        $issueItem->load($item);
        if (!$issueItem->validate() || !$issueItem->save(false)) {
            $this->setErrors("inventory_issue_item", $issueItem->getErrorSummary(true));
        }
    }

    /**
     * @param $issue
     * @return void
     */
    protected function clearIssueItem($issue)
    {
        InventoryIssueItem::deleteAll(["inventory_issue_id" => $issue->id]);
    }


    /**
     * @param $productInventory
     * @param $item
     * @return void
     * @throws Exception
     */
    protected function beforeUpdateProductInventory($productInventory, $item)
    {
        if ($productInventory->available < $item->quantity) {
            $this->setErrors("inventory_issue", "Can not enough quantity to Execute");
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionDone(int $id): array
    {
        $inventoryIssue = InventoryIssue::find()
            ->where(["id" => $id])
            ->pending()
            ->one();
        if (!$inventoryIssue) {
            return ResponseBuilder::responseJson(false, null, "Inventory Issue not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($inventoryIssue->inventoryIssueItem as $item) {
                // Minus incoming inventory_received_id
                // Add Available inventory_received_id
                $inventoryReceived = clone($inventoryIssue);
                // Alias inventory_receive_id to inventory of $inventoryReceived
                $inventoryReceived["inventory_id"] = $inventoryIssue->inventory_receive_id;
                $productInventoryReceived = ProductInventory::interactive($inventoryReceived, $item)
                    ->addAvailable($item->quantity)
                    ->addQuantity($item->quantity);
                $productInventoryReceived->save(false);
                $inventoryReceived->createHistory(
                    InventoryHistory::ACTION_INVENTORY_RECEIVE,
                    $productInventoryReceived->available,
                    $item
                );
                // Minus on way inventory_id
                $productInventoryIssue = ProductInventory::interactive($inventoryIssue, $item);
                $this->beforeUpdateProductInventory($productInventoryIssue, $item);
                $productInventoryIssue
                    ->addAvailable(-$item->quantity)
                    ->addQuantity(-$item->quantity);
                $productInventoryIssue->save(false);
                $inventoryIssue->createHistory(
                    InventoryHistory::ACTION_INVENTORY_ISSUE,
                    $productInventoryIssue->available,
                    $item
                );
            }
            $inventoryIssue->addProgressStatus(InventoryIssue::STATUS_RECEIVE);
            $inventoryIssue->addProgressStatus(InventoryIssue::STATUS_DONE);
            if (!$inventoryIssue->saveDone(false)) {
                $this->setErrors("inventory_issue", $inventoryIssue->getErrorSummary(true));
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["inventory_issue" => $inventoryIssue], "Change status Delivery successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->getErrors()], "Can't Delivery Inventory Issue");
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionCancel(int $id): array
    {
        $inventoryIssue = InventoryIssue::find()
            ->where(["id" => $id])
            ->notCancel()
            ->one();
        if (!$inventoryIssue) {
            return ResponseBuilder::responseJson(false, null, "Inventory Issue not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $inventoryReceived = clone($inventoryIssue);
        // Alias inventory_receive_id to inventory of $inventoryReceived
        $inventoryReceived["inventory_id"] = $inventoryIssue->inventory_receive_id;
        try {
            if ($inventoryIssue->status == InventoryIssue::STATUS_RECEIVE) {
                foreach ($inventoryIssue->inventoryIssueItem as $item) {
                    ProductInventory::interactive($inventoryIssue, $item)
                        ->addOnWay(-$item->quantity)
                        ->addAvailable($item->quantity)
                        ->save(false);
                    ProductInventory::interactive($inventoryReceived, $item)
                        ->addIncoming(-$item->quantity)
                        ->save();
                }
            } else if ($inventoryIssue->status == InventoryIssue::STATUS_DONE) {
                foreach ($inventoryIssue->inventoryIssueItem as $item) {
                    $productInventoryIssue = ProductInventory::interactive($inventoryIssue, $item)
                        ->addAvailable($item->quantity)
                        ->addQuantity($item->quantity);
                    $productInventoryIssue->save(false);
                    $inventoryIssue->createHistory(
                        InventoryHistory::ACTION_CANCEL_INVENTORY_ISSUE,
                        $productInventoryIssue->available,
                        $item
                    );
                    $productInventoryReceived = ProductInventory::interactive($inventoryReceived, $item)
                        ->addAvailable(-$item->quantity)
                        ->addQuantity(-$item->quantity);
                    $productInventoryReceived->save(false);
                    $inventoryIssue->createHistory(
                        InventoryHistory::ACTION_CANCEL_INVENTORY_RECEIPT,
                        $productInventoryReceived->available,
                        $item
                    );
                }
            }
            $inventoryIssue->status = InventoryIssue::STATUS_CANCEL;
            $inventoryIssue->addProgressStatus(InventoryIssue::STATUS_CANCEL);
            if (!$inventoryIssue->save(false)) {
                $this->setErrors("inventory_issue", $inventoryIssue->getErrorSummary(true));
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["inventory_issue" => $inventoryIssue], "Cancel Inventory successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, null, "Can't cancel Inventory Issue ");
        }
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new InventoryIssueSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $inventoryIssue = $this->findModel($id);
        return ResponseBuilder::responseJson(true, ["inventory_issue" => $inventoryIssue]);
    }

    /**
     * @param int $id
     * @return InventoryIssue
     * @throws HttpException
     */
    public function findModel(int $id): InventoryIssue
    {
        $inventoryIssue = InventoryIssue::findOne($id);
        if (!$inventoryIssue) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Inventory Issue not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return $inventoryIssue;
    }

    public function actionExportOverview()
    {
        $dataProvider = (new InventoryIssueSearch())->search(Yii::$app->request->queryParams);
        $inventoryIssues = $dataProvider->query->orderBy(["inventory_issue.id" => SORT_DESC])->all();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getSheet(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle("A1:K1")->getFont()->setBold(true);
        $numRow = 1;
        $numColumn = 1;
        $headers = ["Stt", "Mã Xuất Kho", "Chi Nhánh", "Kho", "Chi Nhánh Tiếp Nhận", "Kho Tiếp Nhận", "Tạo Bởi", "Tổng Số", "Loại", "Ngày Tạo", "Trạng Thái"];
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($numColumn, 1, $header);
            $numColumn++;
        }
        $numColumn = 1;
        $serial = 0;
        /**
         * @var \common\models\InventoryIssue $inventoryIssue
         */
        foreach ($inventoryIssues as $inventoryIssue) {
            $numRow++;
            $serial++;
            $sheet->setCellValueByColumnAndRow(1, $numRow, $serial);
            $sheet->setCellValueByColumnAndRow(2, $numRow, $inventoryIssue->code);
            $sheet->setCellValueByColumnAndRow(3, $numRow, $inventoryIssue->office ? $inventoryIssue->office->name : null);
            $sheet->setCellValueByColumnAndRow(4, $numRow, $inventoryIssue->inventory ? $inventoryIssue->inventory->name : null);
            $sheet->setCellValueByColumnAndRow(5, $numRow, $inventoryIssue->officeReceive ? $inventoryIssue->officeReceive->name : null);
            $sheet->setCellValueByColumnAndRow(6, $numRow, $inventoryIssue->inventoryReceive ? $inventoryIssue->inventoryReceive->name : null);
            $sheet->setCellValueByColumnAndRow(7, $numRow, $inventoryIssue->createdBy->username);
            $sheet->setCellValueByColumnAndRow(8, $numRow, $inventoryIssue->total_number);
            $sheet->setCellValueByColumnAndRow(9, $numRow, $inventoryIssue->getTypeText());
            $sheet->setCellValueByColumnAndRow(10, $numRow, $inventoryIssue->created_at);
            $sheet->setCellValueByColumnAndRow(11, $numRow, $inventoryIssue->getStatusText());
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if (!is_dir("file/exports")) {
            mkdir("file/exports", 0700, true);
        }
        $filename = "file/exports/export_inventory_issue_overview.xlsx";
        $writer->save($filename);
        return Yii::$app->response->sendFile($filename, "export_inventory_issue_overview.xlsx");
    }
}
