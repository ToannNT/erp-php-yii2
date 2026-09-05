<?php

namespace api\modules\v1\admin\product\controllers;

use api\modules\v1\admin\product\models\form\ProductImportForm;
use api\modules\v1\admin\product\models\form\ProductVariantForm;
use common\models\InventoryHistory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\web\Response;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\HttpException;
use common\models\ProductSupplier;
use api\trails\ErrorTrait;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\form\ProductForm;
use api\modules\v1\admin\product\models\Product;
use api\modules\v1\admin\product\models\ProductVariant;
use api\modules\v1\admin\product\models\search\ProductSearch;

class ItemController extends Controller
{
    use ErrorTrait;

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

    /**
     * @return array
     */
    public function actionCreate(): array
    {
        $product = new ProductForm();
        $product->setScenario(ProductForm::SCENARIO_CREATE);
        $product->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$product->validate() || !$product->save()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $product->getErrors()], "Can't Create Product", ApiConstant::STATUS_BAD_REQUEST);
            }
            if (!$product->updateOrCreateTags()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $product->getErrors()], "Can't Create Product Tag", ApiConstant::STATUS_BAD_REQUEST);
            }
            if (!$product->initSuppliers() || !$product->initVariants()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $product->getErrors()], "Can't Create Product Variant", ApiConstant::STATUS_BAD_REQUEST);
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ["product" => $product], "create Product successfully");
        } catch (Exception $e) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, [], "Exception", ApiConstant::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @param ProductForm $product
     * @return void
     */
    protected function clearVariant(ProductForm $product)
    {
        ProductVariant::deleteAll(["product_id" => $product->id]);
    }

    /**
     * @param ProductForm $product
     * @return void
     */
    protected function clearSupplier(ProductForm $product)
    {
        ProductSupplier::deleteAll(["product_id" => $product->id]);
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $product = ProductForm::find()->where(["id" => $id])->unDelete()->one();
        if (!$product) {
            return ResponseBuilder::responseJson(false, null, "Product not found");
        }
        $transaction = Yii::$app->db->beginTransaction();
        $request = Yii::$app->request;
        try {
            /* Load Tags input and Images input validate in model is Array */
            $product->load($request->post());
            if (!$product->validate() || !$product->save()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $product->getErrors()], "Can't Update Product", ApiConstant::STATUS_BAD_REQUEST);
            }
            foreach ((array)$product->variants as $variant) {
                $productVariant = ProductVariantForm::find()->where(["product_id" => $product->id, "id" => $variant["id"] ?? null])->one();
                if (!$productVariant) {
                    $transaction->rollBack();
                    return ResponseBuilder::responseJson(false, [], "Product variant not found", ApiConstant::STATUS_BAD_REQUEST);
                }
                $productVariant->load($variant);
                if (!$productVariant->validate() || !$productVariant->save()) {
                    $transaction->rollBack();
                    return ResponseBuilder::responseJson(false, ["errors" => $productVariant->getErrors()], "Can't update variant", ApiConstant::STATUS_BAD_REQUEST);
                }
            }
            // ===== TEMP: đồng bộ tên + giá product xuống TẤT CẢ variant của product =====
            // Yêu cầu tạm thời, KHÔNG đúng logic biến thể (mỗi biến thể vốn có tên/giá riêng).
            // Dùng updateAll() để ghi thẳng DB, né rule unique `name` khi product có nhiều biến thể.
            // LƯU Ý: không regenerate `slug` của variant. Xoá nguyên block này khi có logic biến thể chuẩn.
            ProductVariant::updateAll(
                [
                    "name"          => $product->name,
                    "unit_price"    => $product->unit_price,
                    "sll_price"     => $product->sll_price,
                    "sku" => $product->sku,
                    "barcode" => $product->bar_code,
                    "compare_price" => $product->compare_price,
                    "images" => json_encode($product->images),
                    "updated_at"    => date("Y-m-d H:i:s"),
                ],
                ["and", ["product_id" => $product->id], ["<>", "status", ProductVariant::STATUS_DELETE]]
            );
            // ===== END TEMP =====
            $product->clearSupplier($product);
            if (!$product->initSuppliers()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $product->getErrors()], "Can't Create Supplier", ApiConstant::STATUS_BAD_REQUEST);
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return ResponseBuilder::responseJson(false, ["errors" => $this->getErrors()], "Can't update Product");
        }
        return ResponseBuilder::responseJson(true, ["product" => $this->findModel($product->id)], "Update Product successfully");
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $product = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("product"));
    }

    /**
     * @param int $id
     * @return array
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $product = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        if (!$product->softDelete()) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, null, "Can't delete");
        }
        // Xoá mềm luôn biến thể. Bỏ sót thì variant vẫn `status = 1`, mọi chỗ query thẳng
        // `product_variant` (giỏ hàng, POS, bộ lọc) vẫn tra ra hàng đã xoá.
        ProductVariant::updateAll(
            ['status' => ProductVariant::STATUS_DELETE, 'deleted_at' => date('Y-m-d H:i:s')],
            ['and', ['product_id' => $product->id], ['<>', 'status', ProductVariant::STATUS_DELETE]]
        );
        $transaction->commit();
        return ResponseBuilder::responseJson(true, null, "Delete Product successfully");
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new ProductSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * Xuất sản phẩm ra Excel **đúng format import** (`docs/product_import_template.xlsx`), để sửa
     * hàng loạt trong Excel rồi import ngược lại — `product-import/import` upsert theo `sku`.
     *
     * Nhận mọi filter của `ProductSearch` (`?status=`, `?product_name=`…) **và phân trang**
     * (`?page=1&per-page=50`). Xuất đúng các bản ghi thuộc trang được chỉ định.
     *
     * `?include_html=1` để kèm `additional_data` dưới dạng cột `html_<tên khối>`. Mặc định tắt vì
     * mỗi khối HTML nặng vài KB, phồng file và ăn RAM nhanh hơn tất cả các cột còn lại cộng lại.
     *
     * Khác `variant/export`: file kia là báo cáo tồn kho theo biến thể (có số lượng), không import
     * ngược được. File này 1 dòng = 1 sản phẩm, khớp đúng quy ước 1 dòng = 1 sản phẩm của import.
     *
     * @throws HttpException
     */
    public function actionExport(): Response
    {
        $includeHtml = (bool)Yii::$app->request->get('include_html');
        $dataProvider = (new ProductSearch())->search(Yii::$app->request->queryParams);
        $dataProvider->query->with(['category', 'brand']);

        // Lấy đúng records theo trang hiện tại thay vì toàn bộ
        $products = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();
        $currentPage = $pagination->getPage() + 1; // Yii pagination 0-indexed

        $htmlColumns = [];
        $rows = [];
        foreach ($products as $product) {
            $row = [
                'name' => $product->name,
                'sku' => $product->sku,
                'bar_code' => $product->bar_code,
                'category_code' => $product->category->code ?? '',
                'category' => $product->category->name ?? '',
                'brand_code' => $product->brand->code ?? '',
                'brand' => $product->brand->name ?? '',
                'unit_price' => $product->unit_price,
                'sll_price' => $product->sll_price,
                'compare_price' => $product->compare_price,
                'import_price' => $product->import_price,
                'weight' => $product->weight,
                'weight_type' => $product->weight_type,
                'dimension' => $product->dimension,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'tags' => implode(',', (array)$product->tags),
                'allow_sell' => $product->allow_sell,
                'status' => $product->status,
                'images' => implode(',', (array)$product->images),
            ];
            if ($includeHtml) {
                foreach ((array)$product->additional_data as $block) {
                    if (empty($block['name'])) {
                        continue;
                    }
                    $column = 'html_' . $block['name'];
                    $htmlColumns[$column] = true;
                    $row[$column] = (string)($block['value'] ?? '');
                }
            }
            $rows[] = $row;
        }

        return $this->sendSpreadsheet(
            array_merge(ProductImportForm::FIXED_COLUMNS, array_keys($htmlColumns)),
            $rows,
            'products_page' . $currentPage . '_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Ghi mảng dòng ra file xlsx rồi trả về cho client.
     *
     * Tên file kèm timestamp: hai người bấm xuất cùng lúc mà ghi đè chung một tên thì người này
     * tải nhầm file của người kia.
     */
    private function sendSpreadsheet(array $headers, array $rows, string $filename): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');

        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        foreach ($rows as $rowIndex => $row) {
            foreach ($headers as $index => $header) {
                $value = $row[$header] ?? '';
                if (in_array($header, ProductImportForm::NUMERIC_COLUMNS, true)) {
                    $sheet->setCellValueByColumnAndRow($index + 1, $rowIndex + 2, $value === '' ? 0 : $value + 0);
                    continue;
                }
                // Ghi dạng chuỗi tường minh: để Excel tự đoán thì mã vạch 10 số thành 1.23E+9,
                // sku dạng "007" mất số 0 đầu, import ngược lại là sai hết.
                $sheet->setCellValueExplicitByColumnAndRow(
                    $index + 1,
                    $rowIndex + 2,
                    (string)$value,
                    DataType::TYPE_STRING
                );
            }
        }

        $directory = Yii::getAlias('@api') . '/web/file/exports';
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new HttpException(500, "Không tạo được thư mục xuất file");
        }
        $path = $directory . '/' . $filename;
        (new Xlsx($spreadsheet))->save($path);

        return Yii::$app->response->sendFile($path, $filename);
    }

    /**
     * @param int $id
     * @return array|\common\models\Product
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if ($product) {
            return $product;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Product not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
