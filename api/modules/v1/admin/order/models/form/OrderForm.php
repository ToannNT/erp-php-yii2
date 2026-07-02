<?php

namespace api\modules\v1\admin\order\models\form;

use Yii;
use common\models\DeliveryFee;
use common\models\Order as OrderAlias;
use yii\base\DynamicModel;
use api\modules\v1\admin\order\models\Order;
use api\modules\v1\frontend\pos\models\Customer;
use common\models\Inventory;
use common\validators\IsArrayValidator;

class OrderForm extends Order
{
    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return false;
        }
        /* delete all order items when change inventory_id or office_id */
        if (isset($this->oldAttributes["inventory_id"]) && $this->oldAttributes["inventory_id"] != $this->inventory_id ||
            isset($this->oldAttributes["office_id"]) && $this->oldAttributes["office_id"] != $this->office_id) {
            $this->deleteAllOrderItems();
        }
        if (isset($this->oldAttributes["office_id"]) && $this->oldAttributes["office_id"] != $this->office_id) {
            $this->inventory_id = null;
        }
        parent::afterValidate();
    }

    public function beforeSave($insert)
    {
        $this->channel = OrderAlias::CHANEL_WEBSITE;
        $this->created_by = Yii::$app->user->getId();
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->setFormatCode();
            $this->addProgressStatus(OrderAlias::STATUS_ORDER);
            if ($this->status == OrderAlias::STATUS_APPROVED) {
                $this->addProgressStatus(OrderAlias::STATUS_APPROVED);
            }
            $this->save(false);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function rules()
    {
        return [
            [["order_address", "shipping_address"], IsArrayValidator::class, "skipOnEmpty" => false],
            [["data_delivery_fee", "tags", "data_tax"], IsArrayValidator::class],
            ["price_policy", "in", "range" => [OrderAlias::UNIT_PRICE, OrderAlias::SLL_PRICE]],
            ["price_policy", "default", "value" => OrderAlias::UNIT_PRICE],
            [["delivery_fee", "total_price", "discount", "payments"], "number", "min" => 0],
            [["note"], "string"],
            [["shipping_address", "order_address"], "addressValidator"],
            ["data_tax", "dataTaxValidator"],
            ["data_delivery_fee", "dataDeliveryFee"],
            [["data_discount"], "required", "on" => self::SCENARIO_ADD_PROMOTION],
            [["data_discount"], IsArrayValidator::class, "on" => self::SCENARIO_ADD_PROMOTION],
            ["status", "default", "value" => OrderAlias::STATUS_ORDER],
            ["status", "in", "range" => [OrderAlias::STATUS_ORDER, OrderAlias::STATUS_APPROVED]],
            ["inventory_id", "inventoryValidator"],
            ["office_id", "safe"],
            ["office_id", "required", "on" => self::SCENARIO_ADD_PROMOTION],
            ["client_id", 'exist', 'targetClass' => Customer::class, 'targetAttribute' => ['client_id' => 'id']],
            ["type", "default", "value" => self::TYPE_ORDER_NORMAL]
        ];
    }

    public function addressValidator($attribute)
    {
        $dynamicModel = new DynamicModel(["name", "phone", "address"]);
        $dynamicModel->addRule(["name", "phone", "address"], "required");
        $dynamicModel->addRule(["name", "phone", "address"], "string");
        $dynamicModel->load($this->$attribute, $this->formName());
        $dynamicModel->validate();
        if ($dynamicModel->hasErrors()) {
            $this->addError($attribute, $dynamicModel->getErrors());
        }
    }

    public function dataTaxValidator($attribute)
    {
        $dynamicModel = new DynamicModel(["tax_value", "tax_reason"]);
        $dynamicModel->addRule(["tax_value", "tax_reason"], "required")
            ->addRule(["tax_value"], "number", ["min" => 0])
            ->addRule(["tax_reason"], "string");
        $dynamicModel->load($this->$attribute, $this->formName());
        $dynamicModel->validate();
        if ($dynamicModel->hasErrors()) {
            $this->addError($attribute, $dynamicModel->getErrorSummary(true));
        }
    }

    public function dataDeliveryFee($attribute)
    {
        $dynamicModel = new DynamicModel(["delivery_fee_id", "delivery_fee_value"]);
        $dynamicModel->addRule(["delivery_fee_value"], "required");
        $dynamicModel->addRule(["delivery_fee_id"], "exist",
            ["targetClass" => DeliveryFee::class, "targetAttribute" => ['delivery_fee_id' => 'id']]
        );
        $dynamicModel->load($this->$attribute, $this->formName());
        $dynamicModel->validate();
        if ($dynamicModel->hasErrors()) {
            $this->addError($attribute, $dynamicModel->getErrors());
            return false;
        }
    }

    public function inventoryValidator($attribute)
    {
        if (isset($this->oldAttributes["office_id"]) && $this->oldAttributes["office_id"] != $this->office_id) {
            $this->inventory_id = null;
            return false;
        }
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
}
