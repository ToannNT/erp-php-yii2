<?php

namespace api\modules\v1\frontend\pos\models\form;

use common\models\Order as OrderAlias;
use common\validators\IsArrayValidator;

class CreateOrderShipping extends CheckoutOrderForm
{
    public $amount;
    public $deposit_amount;
    public $paid_amount;
    public $returned_amount;
    public $order;

    public function rules()
    {
        return [
            [["amount", "paid_amount", "returned_amount", "deposit_amount"], "number"],
            [["amount"], "checkAmount"],
            [["paid_amount"], "checkPaidAmount"],
            [["returned_amount"], "checkReturnedAmount"],
            ["note", "string"],
            ["status", "in", "range" => [OrderAlias::STATUS_ORDER]]
//            ["payment_methods", IsArrayValidator::className(), "skipOnEmpty" => false],
//            ["payment_methods", "paymentValidator"]
        ];
    }

    public function savePaid(): bool
    {
        $this->data_payments = json_encode([
            "amount" => $this->amount,
            "paid_amount" => $this->paid_amount,
            "returned_amount" => $this->returned_amount,
            "deposit_amount" => $this->deposit_amount,
//            "payment_methods" => array_map(function ($payment_method) {
//                return [
//                    "payment_method_id" => $payment_method["payment_method_id"],
//                    "payment_method_name" => $payment_method["payment_method_name"],
//                    "payment" => $payment_method["payment"]
//                ];
//            }, $this->payment_methods)
        ]);
        $this->status = OrderAlias::STATUS_APPROVED;
        $this->addProgressStatus(OrderAlias::STATUS_APPROVED);
        $this->type = OrderAlias::TYPE_ORDER_SHIPPER;
        return $this->save(false);
    }
}