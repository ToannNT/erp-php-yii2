<?php

namespace api\modules\v1\frontend\pos\models\form;

use api\modules\v1\frontend\pos\models\form\UpdateOrderForm as Order;
use common\components\log\BuildLogDbTarget;
use common\components\log\DbTarget;
use common\models\Order as OrderAlias;
use common\models\OrderItem;
use common\models\PaymentMethod;
use common\validators\IsArrayValidator;
use Exception;
use Throwable;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;

class CheckoutOrderForm extends Order
{
    public $payment_methods;
    public $amount;
    public $paid_amount;
    public $returned_amount;
    public $order;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'value' => date("Y-m-d H:i:s"),
            ],
        ]);
    }

    public function rules()
    {
        return [
            ["type", "in", "range" => [OrderAlias::TYPE_ORDER_NORMAL]],
            [["returned_amount", "amount", "paid_amount"], "required"],
            [["amount", "paid_amount", "returned_amount"], "number"],
            [["amount"], "checkAmount"],
            [["paid_amount"], "checkPaidAmount"],
            [["returned_amount"], "checkReturnedAmount"],
            ["note", "string"],
            ["payment_methods", IsArrayValidator::className(), "skipOnEmpty" => false],
            ["payment_methods", "paymentValidator"],
            ["amount", "checkCountOrderItemValidator"],
            ["status", "in", "range" => [OrderAlias::STATUS_ORDER]]
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function paymentValidator($attribute)
    {
        $dynamicModel = new DynamicModel([
            "payment_method_id",
            "payment_method_name",
            "payment"
        ]);
        $dynamicModel->addRule(["payment_method_id", "payment_method_name", "payment"], "required")
            ->addRule(["payment"], "number")
            ->addRule(["payment_method_id"], "exist", ["targetClass" => PaymentMethod::className(), "targetAttribute" => ["payment_method_id" => "id"]]);
        $totalPayment = 0.0;
        foreach ($this->payment_methods as $payment_method) {
            $dynamicModel->load($payment_method, $this->formName());
            if (!$dynamicModel->validate()) {
                $this->addError($attribute, current($dynamicModel->getErrorSummary(true)));
                return false;
            }
            $totalPayment += $dynamicModel->payment;
        }
        if ($totalPayment !== $this->payments) {
            $this->addError($attribute, "total_payment not equal payments");
        }
    }

    public function checkOrderItem($attribute)
    {
        if ($this->quantity <= 0) {
            $this->addError($attribute, "Can't Checkout when Order Item empty");
        }
    }

    public function checkAmount($attribute)
    {
        if ((float)$this->$attribute - $this->payments != 0) {
            $this->addError($attribute, "{$attribute} only accept: {$this->payments}");
        }
    }

    public function checkPaidAmount($attribute)
    {
        if ((float)$this->$attribute < $this->payments) {
            $this->addError($attribute, "{$attribute} only accept: {$this->payments}");
        }
    }

    public function checkReturnedAmount($attribute)
    {
        $returnedAmount = (float)$this->paid_amount - (float)$this->amount;
        if ($returnedAmount != $this->$attribute) {
            $this->addError($attribute, "{$attribute} only accept is: {$returnedAmount}");
        }
    }

    /**
     * @throws Throwable
     */
    public function savePaid(): bool
    {
        $this->data_payments = json_encode([
            "amount" => $this->amount,
            "paid_amount" => $this->paid_amount,
            "returned_amount" => $this->returned_amount,
            "payment_methods" => array_map(function ($payment_method) {
                return [
                    "payment_method_id" => $payment_method["payment_method_id"],
                    "payment_method_name" => $payment_method["payment_method_name"],
                    "payment" => $payment_method["payment"]
                ];
            }, $this->payment_methods)
        ]);
        $this->status = OrderAlias::STATUS_DONE;
        $this->done_at = date("Y-m-d H:i:s");
        $this->addProgressStatus(OrderAlias::STATUS_DONE);
        if (!$this->save(false)) {
            return false;
        }
        (new BuildLogDbTarget())->push("Checkout Order POS", __METHOD__, DbTarget::TAG_UPDATED, [
            "order" => $this->getAttributes(),
            "order_items" => $this->getOrderItems()->asArray()->all(),
            "data_promotion" => $this->getPromotions()->asArray()->all()
        ]);
        return true;
    }

    public function checkCountOrderItemValidator($attribute)
    {
        $numOrderItem = OrderItem::find()->where(["order_id" => $this->id])->count("id");
        if (!$numOrderItem) {
            $this->addError($attribute, "{$attribute} order items empty");
        }
    }

    public function isArrayRule($attribute)
    {
        if (!is_array($this[$attribute])) {
            $this->addError($attribute, "invalid array");
        }
    }
}
