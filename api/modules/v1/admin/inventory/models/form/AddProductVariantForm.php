<?php

namespace api\modules\v1\admin\inventory\models\form;

use common\models\User;
use Yii;
use yii\base\Model;
use common\models\InventoryReceipt;
use api\modules\v1\admin\product\models\ProductVariant;

class AddProductVariantForm extends Model
{
    public $quantity;
    public $sku;
    public $unit_price;
    public $name;
    public $discount_value;
    public $discount_type;
    public $product_id;
    public $product_variant_id;
    public $total_price;
    public $sub_total_price;
    public $discount_price;

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [["sku", "quantity", "unit_price"], "required"],
            ["discount_type", "convertDiscountType"],
            ["sku", "skuValidator"],
            ["quantity", "integer"],
            ["unit_price", "number"],
            [["unit_price", "quantity", "discount_value", "discount_price"], "filter", "filter" => function ($value) {
                return floatval($value);
            }]
        ];
    }


    public function skuValidator($attribute)
    {
        $query = ProductVariant::find()->where(["product_variant.sku" => $this->$attribute])->unDelete();
        if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $query->joinWith("product");
            $query->andWhere(["in", "supplier_id", array_column(Yii::$app->user->identity->suppliers, "id")]);
            $query->groupBy("product_variant.id");
        }
        $productVariant = $query->one();
        if (!$productVariant) {
            $this->addError($attribute, Yii::t("api", "Product Variant {attribute} Not found", [
                "attribute" => $this->$attribute
            ]));
            return false;
        }
        $this->mapToAttributes($productVariant);
    }

    public function mapToAttributes($productVariant)
    {
        $this->name = $productVariant->name;
        $this->product_id = $productVariant->product_id;
        $this->product_variant_id = $productVariant->id;
        $this->sub_total_price = $this->unit_price * $this->quantity;
        if ($this->discount_type == InventoryReceipt::DISCOUNT_PRICE) {
            $this->discount_price = $this->discount_value;
        } else {
            $this->discount_price = $this->sub_total_price * ($this->discount_value / 100);
        }
        $this->total_price = $this->sub_total_price - $this->discount_price;
    }

    public function buildTemplateError($row)
    {
        return "Dòng {$row}: " . (join(", ", array_map(function ($error) {
                return join(",", $error);
            }, $this->getErrors())));
    }

    public function attributeLabels()
    {
        return [
            "quantity" => Yii::t("api", "Quantity"),
            "unit_price" => Yii::t("api", "Unit_price"),
        ];
    }

    public function convertDiscountType($attribute)
    {
        $this->discount_type = [
                "Phần Trăm" => InventoryReceipt::DISCOUNT_PERCENT,
                "Tiền" => InventoryReceipt::DISCOUNT_PRICE
            ][$this->discount_type] ?? InventoryReceipt::DISCOUNT_PERCENT;
    }
}
