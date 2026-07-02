<?php

namespace api\modules\v1\admin\inventory\models\form;

use Yii;
use yii\base\DynamicModel;
use common\models\Inventory;
use common\models\Office;
use common\models\Supplier;
use common\validators\IsArrayValidator;
use api\modules\v1\admin\inventory\models\InventoryReceipt;

class InventoryReceiptForm extends InventoryReceipt
{
    public $receipt_items;

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->getId();
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $this->setFormatCode();
            $this->save(false);
        }
    }

    public function rules()
    {
        return [
            [["office_id", "inventory_id", "supplier_id", "total_price", "sub_total_price", "quantity"], "required"],
            ["total_discount_value", "required", "when" => function ($model) {
                return $model->total_discount_type;
            }],
            ["supplier_id", "exist", "targetClass" => Supplier::class, 'targetAttribute' => ['supplier_id' => 'id'], 'filter' => [
                "=", "status", Supplier::STATUS_ACTIVE
            ]],
            [["total_price", "sub_total_price"], "number", "min" => 1],
            [["quantity"], "integer", "min" => 1],
            [["total_cost", "total_discount_value"], "number"],
            [["total_discount_type"], "in", "range" => [
                InventoryReceipt::DISCOUNT_PERCENT,
                InventoryReceipt::DISCOUNT_PRICE
            ]],
            [["receipt_items", "billing_address", "shipping_address"], IsArrayValidator::class, "skipOnEmpty" => false],
            [["tags", "other_cost"], IsArrayValidator::class],
            [["note"], "string"],
            [["delivery_date"], "string"],
            ["office_id", "exist", "targetClass" => Office::class, 'targetAttribute' => ['office_id' => 'id'], 'filter' => [
                "=", "status", Office::STATUS_ACTIVE
            ]],
            ["inventory_id", "inventoryValidator"],
            ["other_cost", 'otherCostValidator'],
            [["billing_address", "shipping_address"], "billingAddressValidator"],
            ['status', 'in', 'range' => [InventoryReceipt::RECEIPT_STATUS_ORDER, InventoryReceipt::RECEIPT_STATUS_DONE]]
        ];
    }

    public function inventoryValidator($attribute)
    {
        $inventory = Inventory::find()->where([
            "id" => $this->inventory_id
        ])
            ->andWhere([
                "office_id" => $this->office_id
            ])
            ->active()
            ->one();
        if (!$inventory) {
            $this->addError($attribute, "Inventory Or Office not found");
        }
    }

    public function billingAddressValidator($attribute)
    {
        $dynamicModel = new DynamicModel(["name", "phone", "address"]);
        $dynamicModel->addRule(["name", "phone", "address"], "required");
        $dynamicModel->load($this->$attribute, $this->formName());
        $dynamicModel->validate();
        if ($dynamicModel->hasErrors()) {
            $this->addError($attribute, join($dynamicModel->getErrorSummary(true)), ",");
        }
    }

    public function otherCostValidator($attribute)
    {
        $dynamicModel = new DynamicModel(["name", "price", "total_price"]);
        $dynamicModel->addRule(["total_price"], "required")
            ->addRule(["name", "price"], IsArrayValidator::class, ["skipOnEmpty" => false])
            ->addRule("price", "each", ["rule" => ["integer"]])
            ->addRule("name", "each", ["rule" => ["string"]])
            ->addRule("total_price", "integer");
        $dynamicModel->load($this->other_cost, $this->formName());
        $dynamicModel->validate();
        if ($dynamicModel->hasErrors()) {
            $this->addError($attribute, join($dynamicModel->getErrorSummary(true)), ",");
        }
    }

}
