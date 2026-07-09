<?php

namespace common\base\cms;

use common\base\cms\models\Banner;
use common\base\cms\models\Brand;
use common\base\cms\models\Category;
use common\base\cms\models\CategoryBrand;
use common\base\cms\models\Product;
use common\base\cms\models\ProductVariant;
use common\base\cms\models\User;
use common\models\SystemCmsCollection;
use yii\base\BaseObject;

class GetRelationCore extends BaseObject
{
    const TABLE_USER = "users";
    const TABLE_POST = "post";
    const TABLE_PRODUCT = "product";
    const TABLE_PRODUCT_VARIANT = "product_variant";
    const TABLE_CATEGORY = "category";
    const TABLE_BRAND = 'brand';
    const TABLE_CATEGORY_BRAND = 'category_brand';
    const TABLE_BANNER = "banner";


    /**
     * @var mixed
     */
    public $tableName;
    public $rowValues;

    public $type;

    public function result()
    {
        switch ($this->tableName) {
            case self::TABLE_USER:
                $query = User::find()
                    ->where(["id" => $this->rowValues])
                    ->active();
                break;
            case self::TABLE_POST:
                $query = [];
                break;
            case self::TABLE_PRODUCT:
                $query = Product::find()
                    ->where(["id" => $this->rowValues]);
                break;
            case self::TABLE_CATEGORY:
                $query = Category::find()
                    ->where(["id" => $this->rowValues]);
                break;
            case self::TABLE_BRAND:
                $query = Brand::find()
                    ->where(["id" => $this->rowValues]);
                break;
            case self::TABLE_CATEGORY_BRAND:
                $query = CategoryBrand::find()
                    ->where(["id" => $this->rowValues]);
                break;
            case self::TABLE_PRODUCT_VARIANT:
                $query = ProductVariant::find()
                    ->where(["id" => $this->rowValues]);
                break;
            case self::TABLE_BANNER:
                $query = Banner::find()
                    ->where(["id" => $this->rowValues]);
                break;
        }
        if (empty($query)) {
            return false;
        }
        if ($this->type === SystemCmsCollection::TYPE_RELATION_MULTIPLE) {
            return $query->all();
        }
        return $query->one();
    }


    public function getTables()
    {
        return [
            self::TABLE_POST,
            self::TABLE_USER,
            self::TABLE_PRODUCT,
            self::TABLE_PRODUCT_VARIANT,
            self::TABLE_CATEGORY,
            self::TABLE_BRAND,
            self::TABLE_CATEGORY_BRAND,
            self::TABLE_BANNER,
        ];
    }
}
