<?php

namespace api\modules\v1\admin\product\models;

use common\models\ProductInventory as BaseProductInventory;
use yii\db\Query;

class ProductInventory extends BaseProductInventory
{
    public function fields()
    {
        return [
            "id",
            "product_variant" => "productVariant",
            "suppliers" => function () {
                $product = $this->product;
                if ($product) {
                    return $product->suppliers;
                }
                return false;
            },
            "product" => "product",
            "inventory" => "inventory",
            "quantity",
            "sll_price",
            "unit_price",
            "available",
            "incoming",
            "on_way",
            "committed",
            "status",
            "created_at",
            "updated_at",
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getProduct()
    {
        return parent::getProduct()->addSelect(["product.id"])->joinWith(["suppliers" => function (Query $query) {
            $query->addSelect(["id", "name"]);
        }]);
    }

    public function getProducts()
    {
        return parent::getProducts()->addSelect([
            "product.id", "supplier.name"
        ])->addSelect(["product.id"]);
    }

    public function getProductVariants()
    {
        return parent::getProductVariants()->addSelect([
            "id", "name", "slug", "sku", "barcode", "unit_price", "sll_price"
        ]);
    }

    public function getProductVariant()
    {
        return parent::getProductVariant()->addSelect([
            "id", "name", "slug", "sku", "barcode"
        ]);
    }

    public function getInventory()
    {
        return parent::getInventory()->addSelect([
            "id", "name", "code"
        ]);
    }
}
