<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\ProductVariant;
use common\models\InventoryHistory;
use common\models\ProductSupplier;
use common\models\Supplier;
use common\models\Tag;
use Yii;
use yii\behaviors\SluggableBehavior;
use common\validators\IsArrayValidator;
use api\modules\v1\admin\product\models\Product;

class ProductForm extends Product
{
    public $variants;
    public $suppliers;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["slug"] =
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
            ];
        return $behaviors;
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [["product_modifier", "additional_data"], "safe"],
                [["compare_price"], "number", "min" => 0],
                [["compare_price"], "default", "value" => 0],
                [["variants"], IsArrayValidator::class],
                [["product_options", "product_modifier", "tags"], IsArrayValidator::class, 'on' => self::SCENARIO_CREATE],
                [["suppliers"], "each", "rule" => ["integer"]],
                [["images", "tags", "suppliers", "product_options"], "default", "value" => []],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            "variants" => Yii::t("api", "Variants")
        ]);
    }

    public function initSuppliers(): bool
    {
        foreach ($this->suppliers as $supplierId) {
            if (!$this->initSupplier($supplierId)) {
                return false;
            }
        }
        return true;
    }

    public function initSupplier($supplierId): bool
    {
        $supplier = Supplier::find()->andWhere(["id" => $supplierId])->active()->one();
        if (!$supplier) {
            $this->addError("supplier", "Supplier not found");
            return false;
        }
        $productSupplier = new ProductSupplier([
            "product_id" => $this->id,
            "supplier_id" => $supplier->id,
            "status" => Supplier::STATUS_ACTIVE
        ]);
        if (!$productSupplier->save(false)) {
            $this->addError("supplier", "Can't save supplier");
            return false;
        }
        return true;
    }

    public function initVariants(): bool
    {
        if (empty($this->variants)) {
            return $this->initVariant([
                "name" => $this->name,
                "barcode" => $this->bar_code,
                "images" => $this->images,
                "import_price" => $this->import_price,
                "sku" => $this->sku,
                "unit_price" => $this->unit_price,
                "sll_price" => $this->sll_price,
                "compare_price" => $this->compare_price,
            ]);
        }
        foreach ($this->variants as $variant) {
            if (!$this->initVariant($variant)) {
                return false;
            }
        }
        return true;
    }

    public function initVariant($variantParam): bool
    {
        $variant = new ProductVariantForm();
        $variant->product_id = $this->id;
        $variant->load($variantParam);
        $variant->dimension = $this->dimension;
        if (!$variant->validate() || !$variant->save()) {
            $this->addError("variant", $this->flattenErrors($variant, "Không lưu được biến thể."));
            return false;
        }
        if (!$variant->initProductInventories()) {
            $this->addError("variant", $this->flattenErrors($variant, "Không khởi tạo được tồn kho cho biến thể."));
            return false;
        }
        return true;
    }

    /**
     * Gom lỗi của model con thành 1 chuỗi để tránh nhét array vào addError()
     * (nhét array sẽ gây "Array to string conversion" khi implode và che mất lỗi thật).
     */
    private function flattenErrors(\yii\base\Model $model, string $fallback): string
    {
        $summary = $model->getErrorSummary(true);
        return $summary ? implode('; ', $summary) : $fallback;
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
    public function clearSupplier(ProductForm $product)
    {
        ProductSupplier::deleteAll(["product_id" => $product->id]);
    }

    public function updateOrCreateTags(): bool
    {
        foreach ($this->tags as $tagName) {
            $tag = Tag::find()->where(['name' => $tagName])->one();
            if (!$tag) {
                $tag = new Tag();
                $tag->type = Product::TYPE_PRODUCT;
                $tag->name = $tagName;
            } else {
                $tag->popularity += 1;
            }
            if (!$tag->save()) {
                $this->addError("tag", $tag->getErrors());
                return false;
            }
        }
        return true;
    }
}
