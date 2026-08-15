<?php

namespace api\modules\v1\frontend\order\models\form;

use Yii;
use Exception;
use api\modules\v1\admin\inventory\models\ProductVariant;
use api\modules\v1\frontend\pos\models\OrderItem;
use common\models\DiscountCode;
use api\helper\response\ResponseBuilder;
use common\models\DeliveryMethod;
use common\models\Order;
use common\models\PaymentMethod;
use common\validators\IsArrayValidator;

//use api\modules\v1\components\ProductInventory;
use yii\base\Model;

class OrderForm extends Order
{
    public $carts;
    public $firstname;
    public $lastname;
    public $phone;
    public $email;
    public $address;
    public $country;
    public $city;
    public $district;

    /** Phương thức thanh toán khách chọn (1 phương thức) */
    public $payment_method_id;
    /** Số tiền khách đã trả ngay lúc đặt — COD để 0 (mặc định) */
    public $payment;
    /** Trả nhiều phương thức: [{payment_method_id, payment}, ...] — ưu tiên hơn payment_method_id */
    public $payment_methods;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [["carts", "total_price"], "required"],
            [["email"], "email"],
            [["discount", "status"], 'default', 'value' => 0],
            ["price_policy", 'default', 'value' => Order::UNIT_PRICE],
            ["type", 'default', 'value' => Order::TYPE_ORDER_NORMAL],
            ["channel", 'default', 'value' => Order::CHANEL_WEBSITE],
            // phải chạy TRƯỚC checkCartRule vì nó set delivery_fee, mà payments tính có delivery_fee
            ["delivery_method_id", "integer"],
            ["delivery_method_id", "checkDeliveryMethod"],
            ["carts", IsArrayValidator::class],
            ["carts", "checkCartRule"],
            ["payment_method_id", "integer"],
            ["payment_method_id", "checkPaymentMethod"],
            ["payment", "default", "value" => 0],
            ["payment", "number", "min" => 0],
            ["payment_methods", IsArrayValidator::class],
            ["payment_methods", "checkPaymentMethods"],
        ]);
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            "payment_methods" => function () {
                return $this->getMapOrderPaymentMethods();
            },
            "delivery_method" => function () {
                if (!$this->deliveryMethod) {
                    return null;
                }
                return [
                    "id" => $this->deliveryMethod->id,
                    "code" => $this->deliveryMethod->code,
                    "name" => $this->deliveryMethod->name,
                    "fee" => (float)$this->deliveryMethod->fee,
                ];
            },
        ]);
    }

    /**
     * delivery_method_id phải là phương thức đang bật. Không gửi → không tính phí giao,
     * giữ nguyên hành vi cũ (KHÔNG tự lấy phương thức mặc định, vì làm vậy sẽ âm thầm
     * cộng tiền ship khách chưa hề chọn).
     */
    public function checkDeliveryMethod($attribute): bool
    {
        if (empty($this->$attribute)) {
            return true;
        }
        $deliveryMethod = DeliveryMethod::find()->active()->andWhere(["id" => $this->$attribute])->one();
        if (!$deliveryMethod) {
            $this->addError($attribute, "Delivery method {$this->$attribute} is invalid");
            return false;
        }
        // snapshot phí tại thời điểm đặt — admin đổi giá sau này không ảnh hưởng đơn cũ
        $this->delivery_fee = (float)$deliveryMethod->fee;
        return true;
    }

    public function checkCartRule($attribute)
    {
        $totalCheck = 0;
        $quantity = 0;
        foreach ($this->carts as &$cart) {
            $orderItemForm = new OrderItemForm();
            if (!$orderItemForm->load($cart, '') || !$orderItemForm->validate()) {
                $this->addError($attribute, $orderItemForm->getErrors());
                return false;
            }
            //check total product variant
            $totalCheck += $orderItemForm->unit_price * $orderItemForm->quantity;
            $quantity += $orderItemForm->quantity;
        }
        if ($totalCheck != $this->total_price) {
            $this->addError($attribute, "Total amount invalid");
            return false;
        }
        $this->total_price = $totalCheck;
        $this->quantity = $quantity;
        /*payments = total_price + tax_price + delivery_fee - discount*/
        $this->payments = strval($this->total_price - $this->discount + (float)$this->delivery_fee);
        return true;
    }

    /**
     * payment_method_id phải là phương thức đang bật (status active).
     */
    public function checkPaymentMethod($attribute): bool
    {
        if (empty($this->$attribute)) {
            return true;
        }
        if (!PaymentMethod::find()->active()->andWhere(["id" => $this->$attribute])->exists()) {
            $this->addError($attribute, "Payment method {$this->$attribute} is invalid");
            return false;
        }
        return true;
    }

    /**
     * Mảng trả nhiều phương thức: mỗi phần tử cần payment_method_id hợp lệ + payment >= 0,
     * tổng tiền trả không được vượt quá payments của đơn.
     */
    public function checkPaymentMethods($attribute): bool
    {
        if (empty($this->$attribute)) {
            return true;
        }
        $totalPayment = 0;
        foreach ($this->$attribute as $index => $paymentMethod) {
            $paymentMethodId = $paymentMethod["payment_method_id"] ?? null;
            $payment = $paymentMethod["payment"] ?? null;
            if (empty($paymentMethodId) || !is_numeric($payment) || $payment < 0) {
                $this->addError($attribute, "payment_methods[$index] requires payment_method_id and payment >= 0");
                return false;
            }
            if (!PaymentMethod::find()->active()->andWhere(["id" => $paymentMethodId])->exists()) {
                $this->addError($attribute, "Payment method $paymentMethodId is invalid");
                return false;
            }
            $totalPayment += (float)$payment;
        }
        if ($totalPayment > (float)$this->payments) {
            $this->addError($attribute, "Total payment $totalPayment greater than payments {$this->payments}");
            return false;
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function saveOrderItem(): bool
    {
        foreach ($this->carts as $cartItem) {
            $orderItem = new OrderItemForm();
            $orderItem->load($cartItem, '');
            $orderItem->order_id = $this->id;
            $orderItem->discount_price = OrderItemForm::DEFAULT_DISCOUNT_PRICE;
            $orderItem->calculate();
            if (!$orderItem->save()) {
                $this->addError($cartItem, $orderItem->getErrors());
                return false;
            }
        }
        return true;
    }

    /**
     * Ghi nhận phương thức thanh toán khách chọn vào order_payment_method.
     * Thứ tự ưu tiên:
     *  1. payment_methods (mảng) — khách trả bằng nhiều phương thức
     *  2. payment_method_id  — 1 phương thức, số tiền đã trả = payment (mặc định 0 = COD chưa thu)
     *  3. không gửi gì       — lấy phương thức mặc định (is_default) để đơn luôn có thông tin thanh toán
     * @throws Exception
     */
    public function saveOrderPaymentMethods(): bool
    {
        $paymentMethods = $this->payment_methods;
        if (empty($paymentMethods)) {
            $paymentMethodId = $this->payment_method_id ?: PaymentMethod::findDefault()?->id;
            if (!$paymentMethodId) {
                return true;
            }
            $paymentMethods = [
                [
                    "payment_method_id" => $paymentMethodId,
                    "payment" => (float)$this->payment
                ]
            ];
        }
        $this->savePaymentMethods($paymentMethods);
        return true;
    }
}
