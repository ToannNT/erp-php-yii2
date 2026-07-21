<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\Product;
use common\models\Brand;
use common\models\Category;
use Throwable;
use Yii;

class ProductImportForm extends ImportForm
{
    protected array $allowedColumns = ['name'];

    public function import(): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = parent::import();
        } catch (Throwable $e) {
            $transaction->rollBack();
            return ['success' => 0, 'skipped' => 0, 'errors' => ['Import lỗi: ' . $e->getMessage()], 'rolled_back' => true];
        }

        if (!empty($result['errors'])) {
            $transaction->rollBack();
            $result['success'] = 0;
            $result['rolled_back'] = true;
        } else {
            $transaction->commit();
            $result['rolled_back'] = false;
        }

        return $result;
    }

    /**
     * Tra cứu id của category/brand: ưu tiên theo `code`, nếu trống thì fallback theo `name`.
     * @return int|null|false  int = id tìm thấy; null = không khai báo (bỏ qua); false = có khai báo nhưng không tồn tại.
     */
    private function resolveRef(string $modelClass, string $code, string $name, int $deleteStatus)
    {
        if ($code === '' && $name === '') {
            return null;
        }
        $query = $modelClass::find()->andWhere(['<>', 'status', $deleteStatus]);
        if ($code !== '') {
            $query->andWhere(['code' => $code]);
        } else {
            $query->andWhere(['name' => $name]);
        }
        $model = $query->one();
        return $model ? $model->id : false;
    }

    protected function processRow(array $data, int $rowIndex): ?string
    {
        $name = $data['name'] ?? '';
        if ($name === '') {
            return 'Cột "name" không được trống.';
        }

        $sku = $data['sku'] ?? '';
        if ($sku !== '') {
            $exists = Product::find()
                ->where(['sku' => $sku])
                ->andWhere(['<>', 'status', Product::STATUS_DELETE])
                ->exists();
            if ($exists) {
                return null;
            }
        } else {
            $sku = $this->generateUnique('sku', 100000000, 999999999);
        }

        $barCode = $data['bar_code'] ?? '';
        if ($barCode === '') {
            $barCode = $this->generateUnique('bar_code', 1000000000, 9999999999);
        }

        $categoryId = $this->resolveRef(
            Category::class,
            $data['category_code'] ?? '',
            $data['category'] ?? '',
            Category::STATUS_DELETE
        );
        if ($categoryId === false) {
            return 'Category không tồn tại (code="' . ($data['category_code'] ?? '') . '", name="' . ($data['category'] ?? '') . '").';
        }

        $brandId = $this->resolveRef(
            Brand::class,
            $data['brand_code'] ?? '',
            $data['brand'] ?? '',
            Brand::STATUS_DELETE
        );
        if ($brandId === false) {
            return 'Brand không tồn tại (code="' . ($data['brand_code'] ?? '') . '", name="' . ($data['brand'] ?? '') . '").';
        }

        $tags = !empty($data['tags'])
            ? array_values(array_filter(array_map('trim', explode(',', $data['tags']))))
            : [];

        $form = new ProductForm();
        $form->setScenario(ProductForm::SCENARIO_CREATE);
        $form->setAttributes([
            'name'              => $name,
            'sku'               => $sku,
            'type'              => Product::TYPE_PRODUCT,
            'bar_code'          => $barCode,
            'category_id'       => $categoryId,
            'brand_id'          => $brandId,
            'unit_price'        => $this->toFloat($data['unit_price'] ?? null),
            'sll_price'         => $this->toFloat($data['sll_price'] ?? null),
            'compare_price'     => $this->toFloat($data['compare_price'] ?? null),
            'import_price'      => $this->toFloat($data['import_price'] ?? null),
            'weight'            => $this->toFloat($data['weight'] ?? null),
            'weight_type'       => $data['weight_type'] ?? '',
            'dimension'         => $data['dimension'] ?? '',
            'short_description' => $data['short_description'] ?? null,
            'description'       => $data['description'] ?? null,
            'tags'              => $tags,
            'allow_sell'        => $this->toInt($data['allow_sell'] ?? null, Product::STATUS_INACTIVE),
            'status'            => $this->toInt($data['status'] ?? null, Product::STATUS_ACTIVE),
        ]);

        try {
            if (!$form->validate() || !$form->save()) {
                return implode(', ', $form->getFirstErrors());
            }
            $form->updateOrCreateTags();
            if (!$form->initVariants()) {
                return implode(', ', $form->getFirstErrors());
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }

        return null;
    }

    private function generateUnique(string $column, int $min, int $max): string
    {
        do {
            $value = (string)random_int($min, $max);
        } while (Product::find()->where([$column => $value])->exists());
        return $value;
    }

    private function toFloat($value): float
    {
        return ($value === null || $value === '') ? 0 : (float)$value;
    }

    private function toInt($value, int $default): int
    {
        return ($value === null || $value === '') ? $default : (int)$value;
    }
}
