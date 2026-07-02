<?php

namespace common\models;

use Yii;
use \common\models\base\Product as BaseProduct;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class Product
 * @property ProductVariant[] $productVariants
 * @property Category $category
 * @property Brand $brand
 * @property string $quantityHtml
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Product extends BaseProduct
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;
    const STATUS_ALLOW_SELL = 1;
    const TYPE_PRODUCT = "product";

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public function attributeLabels()
    {
        return [
            "name" => Yii::t("api", "Product Name"),
            "sku" => Yii::t("api", "Sku"),
            "bar_code" => Yii::t("api", "BarCode"),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductVariants($selects = [])
    {
        return $this->hasMany(ProductVariant::class, ['product_id' => 'id'])
            ->select($selects)
            ->where(['!=', ProductVariant::tableName() . ".status", ProductVariant::STATUS_DELETE])
            ->groupBy(ProductVariant::tableName() . ".name");
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory($selects = [])
    {
        return $this->hasOne(Category::class, ['id' => 'category_id'])
            ->select($selects);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['id' => "brand_id"]);
    }

    public function getBrandName(): string
    {
        return $this->brand->name ?? "";
    }

    public function getCategoryName(): string
    {
        return $this->category->name ?? "";
    }

    public function getColors($selects = ["id", "name", "value"]): array
    {
        $product_variants = ProductVariant::find()->where(["name" => $this->name])->all();
        return Color::find()->where(["in", "id", array_column($product_variants, "color_id")])
            ->select($selects)
            ->all();
    }

    public function getRating(): float
    {
        return round($this->hasOne(Comment::class, ["module_id" => "id"])
            ->where(["type" => Comment::TYPE_PRODUCT])
            ->andWhere(["status" => Comment::STATUS_ACTIVE])
            ->average("rating"), 1);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getSuppliers()
    {
        return $this->hasMany(Supplier::class, ["id" => "supplier_id"])
            ->viaTable("product_supplier", ["product_id" => "id"]);
    }

    public function getProductMeta(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductMetum::class, ["product_id" => "id"]);
    }

    public function getGroupSuppliers()
    {
        $str = '';
        foreach ($this->suppliers as $supplier) {
            $str .= $supplier->name;
        }
        return $str;
    }

    public function getImages()
    {
        return json_decode($this->images);
    }

    public function getTags()
    {
        return json_decode($this->tags);
    }


    public function getAvailable()
    {
        return $this->hasMany(ProductInventory::class, ['product_id' => 'id'])->sum('available');
    }

    public function getQuantity()
    {
        return $this->hasMany(ProductInventory::class, ['product_id' => 'id']);
    }

    public function getProductInventory()
    {
        return $this->hasMany(ProductInventory::class, ['product_id' => 'id']);
    }
}
