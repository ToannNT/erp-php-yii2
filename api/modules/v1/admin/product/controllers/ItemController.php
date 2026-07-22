<?php

namespace api\modules\v1\admin\product\controllers;

use api\modules\v1\admin\product\models\form\ProductVariantForm;
use common\models\InventoryHistory;
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
            if(!$product->updateOrCreateTags()){
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
                    "compare_price" => $product->compare_price,
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
        if (!$product->softDelete()) {
            return ResponseBuilder::responseJson(false, null, "Can't delete");
        }
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
