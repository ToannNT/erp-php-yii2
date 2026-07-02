<?php

namespace api\modules\v1\admin\product\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\Product;
use api\modules\v1\admin\product\models\ProductInventory;
use api\modules\v1\admin\product\models\ProductVariant;
use api\modules\v1\admin\product\models\search\ProductInventorySearch;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;

class InventoryController extends Controller
{

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $productInventory = ProductInventory::find()->where(["id" => $id])->one();
        if ($productInventory) {
            return ResponseBuilder::responseJson(true, ["product_inventory" => $productInventory]);
        }
        return ResponseBuilder::responseJson(false, null, "Product Inventory Not found");
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new ProductInventorySearch())->search(Yii::$app->request->queryParams));
    }

    public function actionExport()
    {
        $dataProvider = (new ProductInventorySearch())->search(Yii::$app->request->queryParams);
        $productInventories = $dataProvider->query->all();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getSheet(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle("A1:I1")->getFont()->setBold(true);
        $numRow = 1;
        $numColumn = 1;
        $headers = ["Stt", "Chi Nhánh", "Kho", "Nhà Cung Cấp", "Mã SKU", "Tên Sản Phẩm", "Có Sẵn", "Đơn Giá(VNĐ)", "Mã Vạch",];
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($numColumn, 1, $header);
            $numColumn++;
        }
        $numColumn = 1;
        $serial = 0;
        /**
         * @var \common\models\ProductInventory $productInventory
         */
        foreach ($productInventories as $productInventory) {
            $numRow++;
            $serial++;
            $sheet->setCellValueByColumnAndRow(1, $numRow, $serial);
            $sheet->setCellValueByColumnAndRow(2, $numRow, $productInventory->office->name);
            $sheet->setCellValueByColumnAndRow(3, $numRow, $productInventory->inventory->name);
            $sheet->setCellValueByColumnAndRow(4, $numRow, $productInventory->product
                ? join(",", array_column($productInventory->product->suppliers, "name")) : "");
            $sheet->setCellValueByColumnAndRow(5, $numRow, $productInventory->productVariant->sku);
            $sheet->setCellValueByColumnAndRow(6, $numRow, $productInventory->productVariant->name);
            $sheet->setCellValueByColumnAndRow(7, $numRow, $productInventory->available);
            $sheet->setCellValueByColumnAndRow(8, $numRow, $productInventory->unit_price);
            $sheet->setCellValueByColumnAndRow(9, $numRow, $productInventory->productVariant->barcode);
        }
        $writer = new Xlsx($spreadsheet);
        if (!is_dir("file/exports")) {
            mkdir("file/exports", 0700, true);
        }
        $filename = "file/exports/export_inventory_overview.xlsx";
        $writer->save('file/exports/export_inventory_overview.xlsx');
        return Yii::$app->response->sendFile($filename, "export_inventory_overview.xlsx");
    }
}
