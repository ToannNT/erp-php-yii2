<?php

namespace api\modules\v1\admin\setting\controllers;

use Yii;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use api\modules\v1\admin\setting\models\form\ImportProductPromotionForm;
use common\models\ProductVariant;
use yii\db\Exception;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\Promotion;
use api\modules\v1\admin\setting\models\search\PromotionSearch;
use yii\web\UploadedFile;

class PromotionController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "access" => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administrator', 'manager'],
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
        $promotion = new Promotion();
        $promotion->load(Yii::$app->request->post());
        if (!$promotion->validate() || !$promotion->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $promotion->getErrorSummary(true)], current($promotion->getErrorSummary(true)));
        }
        return ResponseBuilder::responseJson(true, compact("promotion"), "Create Promotion successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $promotion = $this->findModel($id);
        $promotion->load(Yii::$app->request->post());
        if (!$promotion->validate() || !$promotion->save()) {
            return ResponseBuilder::responseJson(false, ["errors" => $promotion->getErrorSummary(true)], current($promotion->getErrorSummary(true)));
        }
        return ResponseBuilder::responseJson(true, compact("promotion"), "Update Promotion successfully");
    }


    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $promotion = $this->findModel($id);
        if ($promotion->softDelete()) {
            return ResponseBuilder::responseJson(true, null, "Delete Promotion successfully");
        }
        return ResponseBuilder::responseJson(false, null, "Can't Delete Promotion");
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new PromotionSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $promotion = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("promotion"));
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $promotion = Promotion::find()->andWhere(compact("id"))->unDelete()->one();
        if ($promotion) {
            return $promotion;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Promotion not found", ApiConstant::STATUS_NOT_FOUND);
    }

    public function actionImport()
    {
        $model = new ImportProductPromotionForm([
            "file" => UploadedFile::getInstanceByName("file")
        ]);
        $errors = [];
        try {
            if (!$model->validate()) {
                $errors[] = $model->getErrorSummary(true);
                throw new Exception("Invalid Input");
            }
            if (!is_dir(ImportProductPromotionForm::DIR_IMPORT)) {
                mkdir(ImportProductPromotionForm::DIR_IMPORT, 0755, true);
            }
            $filename = $model->getFilename();
            $model->file->saveAs($filename);
            $productVariants = $this->genarateProductByExcel($filename, $errors);
            if ($errors) {
                throw new Exception("Invalid Excel");
            }
            return ResponseBuilder::responseJson(true, ["product_variants" => $productVariants]);
        } catch (\Exception $e) {
            return ResponseBuilder::responseJson(false, ["errors" => $errors,], Yii::t("api", "Can't get variants"));
        }
    }

    public
    function actionDownloadTemplateImport()
    {
        $filename = "file/templates/template_import_product_promotion.xlsx";
        return Yii::$app->response->sendFile($filename, "template_import_product_promotion.xlsx");
    }

    protected
    function genarateProductByExcel($filename, &$errors)
    {
        $variants = [];
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            if (!strval($sheet->getCellByColumnAndRow(ImportProductPromotionForm::INDEX_SKU, $row))) {
                continue;
            }
            $sku = strval($sheet->getCellByColumnAndRow(ImportProductPromotionForm::INDEX_SKU, $row));
            $productVariant = ProductVariant::find()->where(["sku" => $sku])
                ->joinWith("suppliers")
                ->andFilterWhere(["supplier.name" => Yii::$app->request->get("supplier_name")])
                ->unDelete()->addSelect(["product_variant.id", "product_variant.name", "sku"])->one();
            if (!$productVariant) {
                $errors[] = "Dòng {$row} SKU:{$sku} Không tìm thấy";
                continue;
            }
            $variants[] = $productVariant;
        }
        return $variants;
    }
}
