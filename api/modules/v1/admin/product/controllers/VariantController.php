<?php

namespace api\modules\v1\admin\product\controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use api\helper\response\ApiConstant;
use api\modules\v1\admin\product\models\search\ProductSearch;
use common\models\Product;
use Exception;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Yii;
use yii\filters\AccessControl;
use yii\rest\Controller;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\form\ProductVariantForm;
use api\modules\v1\admin\product\models\form\InitProductInventory;
use api\modules\v1\admin\product\models\ProductVariant;
use api\modules\v1\admin\product\models\search\ProductVariantSearch;
use yii\web\HttpException;
use \common\models\ProductVariant as BaseProductVariant;

class VariantController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'except' => ['index', 'view'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['administrator', 'manager'],
                ],
            ],
        ];
        return $behaviors;
    }

    protected $errors;

    public function actionCreate(): array
    {
        $variant = new ProductVariantForm();
        $variant->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$variant->validate() || !$variant->save()) {
                $this->setErrors("product.variant", $variant->getErrors());
            }
            if ($variant->inventories) {
                foreach ($variant->inventories as $inventory) {
                    $this->initProductInventory($inventory, $variant);
                }
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["variant" => $this->findModel($variant->id)], "Create Variant successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $this->errors]);
        }
    }

    public function actionUpdate(int $id): array
    {
        $variant = ProductVariantForm::find()->where(["id" => $id])->unDelete()->one();
        if (!$variant) {
            return ResponseBuilder::responseJson(false, null, "Variant not found");
        }
        $variant->load(Yii::$app->request->post());
        if (!$variant->validate() || !$variant->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $variant->getErrors()], "Can't update Variant");
        }
        return ResponseBuilder::responseJson(true, ["variant" => $this->findModel($variant->id)], "Update Variant successfully");
    }

    public function actionView(int $id): array
    {
        $variant = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("variant"));
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {

        return ResponseBuilder::responseJson(true, (new ProductVariantSearch())->search(Yii::$app->request->queryParams));
    }

    public function actionDelete(int $id)
    {
        $variant = $this->findModel($id);
        if ($variant->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Variant successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't delete Variant");
    }

    /**
     * @throws HttpException
     */
    public function actionGetBarcode(): array
    {
        $result = [];
        $base64src = "data:image/png;base64,";
        $barcodes = explode(",", Yii::$app->request->get("barcodes", ""));
        $generator = new BarcodeGeneratorPNG();
        foreach ($barcodes as $barcode) {
            if ($barcode) {
                $result[] = $base64src . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 80));
            }
        }
        return ResponseBuilder::responseJson(true, $result);
    }

    /**
     * @throws HttpException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function actionExport()
    {
        $dataProvider = (new ProductSearch())->search(Yii::$app->request->queryParams);
        $products = $dataProvider->query->select(["product.id"])->all();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getSheet(0);
        $sheet = $spreadsheet->getActiveSheet();
        $numRow = 1;
        $serial = 1;
        $numColumn = 1;
        $headers = ["Stt", "Mã SKU", "Tên Sản Phẩm", "Số Lượng", "Đơn Giá(VNĐ)", "Mã Vạch", "Nhà Cung Cấp"];
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($numColumn, $numRow, $header);
            $numColumn++;
        }
        $numColumn = 1;
        foreach ($products as $product) {
            /**
             * @var ProductVariant $productVariant
             * @var \api\modules\v1\admin\product\models\Product $product ;
             */
            foreach ($product->productVariants as $productVariant) {
                $numRow++;
                $sheet->setCellValueByColumnAndRow(1, $numRow, $serial);
                $sheet->setCellValueByColumnAndRow(2, $numRow, $productVariant->sku);
                $sheet->setCellValueByColumnAndRow(3, $numRow, $productVariant->name);
                $sheet->setCellValueByColumnAndRow(4, $numRow, $productVariant->getQuantity() ?? 0);
                $sheet->setCellValueByColumnAndRow(5, $numRow, $productVariant->unit_price);
                $sheet->setCellValueByColumnAndRow(6, $numRow, $productVariant->barcode);
                $sheet->setCellValueByColumnAndRow(7, $numRow, $product->getSuppliers()->one()->name);
                $serial++;
            }
        }
        $writer = new Xlsx($spreadsheet);
        if (!is_dir("file/exports")) {
            mkdir("file/exports", 0700, "true");
        }
        $filename = "file/exports/export_product_variant_overview.xlsx";
        $writer->save($filename);
        return Yii::$app->response->sendFile($filename, "export_product_variant_overview.xlsx");
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $variant = ProductVariant::find()->where(["id" => $id])->unDelete()->one();
        if (!$variant) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Variant not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return $variant;
    }

    protected function initProductInventory(array $inventoryParams, ProductVariantForm $variant)
    {
        $productInventory = new initProductInventory();
        $productInventory->load($inventoryParams);
        $productInventory->variant = $variant;
        if (!$productInventory->initInventory()) {
            $this->setErrors("product_inventory", $productInventory->getErrors());
        }
    }

    public function setErrors($key, $errors)
    {
        $this->errors = [
            $key => $errors
        ];
        throw new Exception(json_encode($errors));
    }
}
