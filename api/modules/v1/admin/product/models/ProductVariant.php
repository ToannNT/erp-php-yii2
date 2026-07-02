<?php

namespace api\modules\v1\admin\product\models;

use common\behaviors\JsonBehavior;
use common\models\User;
use common\models\ProductVariant as BaseProductVariant;
use common\traits\SoftDeleteTrait;
use Yii;

class ProductVariant extends BaseProductVariant
{
    use SoftDeleteTrait;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["images", "meta_field"]
        ];
        return $behaviors;
    }

    public function fields()
    {
        return [
            "id",
            "name",
            "slug",
            "product_id",
            "sku",
            "barcode",
            "unit_price",
            "quantity" => function () {
                return $this->getProductInventories()->sum("quantity");
            },
            "suppliers" => function () {
                return $this->getSuppliers()->addSelect("name")->all();
            },
            "sll_price",
            "import_price",
            "weight",
            "weight_type",
            "dimension",
            "images",
            "created_at",
            "updated_at",
        ];
    }

    public function extraFields()
    {
        return [
            "inventories" => "productInventories",
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getImages()
    {
        return $this->images = json_decode($this->images);
    }

    public function getProductInventories()
    {
        /**
         * Add role manager
         */
        $query = $this->hasMany(ProductInventory::class, ["product_variant_id" => "id"]);
        if (Yii::$app->user->can(User::ROLE_MANAGER)) {
            $inventories = Yii::$app->user->identity->inventorys;
            $query->andWhere(["product_inventory.inventory_id" => array_column($inventories, "id")]);
        }
        return $query;
    }

    public function getProduct($selects = [])
    {
        return parent::getProduct($selects)
            ->joinWith("suppliers");
    }
}
