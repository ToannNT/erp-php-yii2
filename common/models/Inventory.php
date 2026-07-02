<?php

namespace common\models;

use Yii;
use \common\models\base\Inventory as BaseInventory;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Class Inventory
 * @property Office $office
 * @property User $owner
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Inventory extends BaseInventory
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;

    const DISCOUNT_PRICE = 2;
    const DISCOUNT_PERCENT = 1;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'softDeleteBehavior' => [
                    'class' => SoftDeleteBehavior::className(),
                    'softDeleteAttributeValues' => [
                        'status' => Inventory::STATUS_DELETE,
                        'deleted_at' => date("Y-m-d H:i:s")
                    ],
                ]
            ]
        );
    }

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'INVN' . $tmp;
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
            "name" => Yii::t("api", "Name"),
            "description" => Yii::t("api", "Description")
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    public function getUserOffices()
    {
        return $this->hasMany(UserOffice::class, ["office_id" => "id"])
            ->viaTable("office", ["id" => "office_id"]);
    }

    public function getProductInventories()
    {
        return $this->hasMany(ProductInventory::class, ["inventory_id" => "id"]);
    }

    public function getProductVariants()
    {
        return $this->hasMany(ProductVariant::class, ["id" => "product_variant_id"])
            ->viaTable("product_inventory", ["inventory_id" => "id"]);
    }

    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ["id" => "product_variant_id"])
            ->viaTable("product_inventory", ["inventory_id" => "id"]);
    }

    public function withRole()
    {
        $query = self::find()->active();
        if (Yii::$app->user->can("manager")) {
            $query->andWhere(["id" => Yii::$app->user->identity->inventorys]);
        }
        return $query;
    }
}
