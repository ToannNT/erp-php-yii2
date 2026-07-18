<?php

namespace api\modules\v1\admin\product\models;

use common\behaviors\JsonBehavior;
use common\models\Brand;
use common\models\Category;
use common\models\Product as BaseProduct;
use common\traits\SoftDeleteTrait;
use yii\db\ActiveQuery;

class Product extends BaseProduct
{
    use SoftDeleteTrait;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["images", "tags", "product_options", "product_modifier", "additional_data"]
        ];
        return $behaviors;
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "sku",
            "bar_code",
            "category" => "category",
            "brand" => "brand",
            "suppliers" => "suppliers",
            "slug",
            "unit_price",
            "compare_price",
            "images",
            "tags",
            "sll_price",
            "import_price",
            "has_tax",
            "dimension",
            "allow_sell",
            "product_variants" => "productVariants",
            "weight",
            "additional_data",
            "weight_type",
            "description",
            "short_description",
            "status",
            "created_at",
            "updated_at",
        ];
    }

    //    public function extraFields()
    //    {
    //        return [
    //            "id",
    //            "name",
    //            "sku",
    //            "bar_code",
    //            "category" => "category",
    //            "brand" => "brand",
    //            "suppliers" => "suppliers",
    //            "slug",
    //            "unit_price",
    //            "images",
    //            "tags",
    //            "sll_price",
    //            "import_price",
    //            "has_tax",
    //            "dimension",
    //            "allow_sell",
    //            "product_variants" => "productVariants",
    //            "weight",
    //            "weight_type",
    //            "description",
    //            "short_description",
    //            "status",
    //            "created_at",
    //            "updated_at",
    //        ];
    //    }

    public function rules()
    {
        return [
            [["name", "sku"], "required"],
            [["name", "sku", "bar_code"], "unique", "filter" => [
                "!=",
                "status",
                Product::STATUS_DELETE
            ]],
            [["category_id"], "exist", 'targetClass' => Category::class, "filter" => [
                "=",
                "status",
                Category::STATUS_ACTIVE
            ], 'targetAttribute' => ['category_id' => 'id']],
            [["brand_id"], "exist", "targetClass" => Brand::class, "filter" => [
                "=",
                "status",
                Brand::STATUS_ACTIVE
            ], 'targetAttribute' => ['brand_id' => 'id']],
            [["description"], "string"],
            [["short_description"], "string"],
            [["import_price", "unit_price", "sll_price", "weight"], "number", "min" => 0],
            [["import_price", "unit_price", "sll_price", "weight"], "default", "value" => 0],
            [["weight_type"], "in", "range" => ["kg", "g"]],
            [["dimension"], "string"],
            [["allow_sell"], "in", "range" => [Product::STATUS_INACTIVE, Product::STATUS_ALLOW_SELL]],
            [["allow_sell"], "default", "value" => Product::STATUS_INACTIVE],
            [["status"], "default", "value" => Product::STATUS_ACTIVE],
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getCategory($selects = [])
    {
        return $this->hasOne(Category::class, ["id" => "category_id"])->addSelect([
            "id",
            "name",
            "code",
            "slug"
        ]);
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, ["id" => "brand_id"])->addSelect([
            "id",
            "name",
            "code",
            "slug",
        ]);
    }

    public function getProductVariants($selects = [])
    {
        return $this->hasMany(ProductVariant::class, ["product_id" => "id"])->andOnCondition([
            "<>",
            "product_variant.status",
            ProductVariant::STATUS_DELETE
        ]);
    }

    public function getSuppliers()
    {
        return parent::getSuppliers()->addSelect(["id", "name"]);
    }
}
