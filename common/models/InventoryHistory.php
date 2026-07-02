<?php

namespace common\models;

use Yii;
use \common\models\base\InventoryHistory as BaseInventoryHistory;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class InventoryHistory
 * @property User $createdBy
 * @property Inventory $modelInventory
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class InventoryHistory extends BaseInventoryHistory
{
    const ACTION_INIT_VARIANT = 'Init product variant';
    const ACTION_BALANCE = 'Inventory balance';
    const ACTION_ORDER = 'Delivery warehouse for guests / shipper';
    const ACTION_CANCEL_ORDER = "Cancel Order";
    const ACTION_INVENTORY_ISSUE = 'Inventory Issue';
    const ACTION_INVENTORY_RECEIPT = 'Inventory Receipt';
    const ACTION_INVENTORY_RECEIVE = 'Inventory Received';
    const ACTION_INVENTORY_ISSUE_FOR_ORDER = "Inventory Issue for Order";
    const ACTION_INVENTORY_ISSUE_FOR_POS = "Inventory Issue for Order Pos";
    const ACTION_CANCEL_INVENTORY_RECEIPT = "Cancel Inventory Receipt";
    const ACTION_CANCEL_INVENTORY_ISSUE = "Cancel Inventory Issue";
    const TYPE_INVENTORY_RECEIPT = 1;
    const TYPE_INVENTORY_ISSUE = 2;
    const TYPE_INVENTORY_STOCKTAKING = 4;
    const TYPE_ORDER = 3;
    const TYPE_RETURN = 5;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    /**
     * @throws Exception
     */
    public static function create($data)
    {
        $model = new self($data);
        if (!$model->save(false)) {
            throw new Exception();
        }
        return $model;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelInventory()
    {
        return $this->hasOne(Inventory::class, ['id' => 'inventory_id']);
    }

    public function getModelOffice()
    {
        return $this->hasOne(Office::class, ["id" => "office_id"])
            ->via("modelInventory");
    }

    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ["id" => "product_variant_id"]);
    }
}
