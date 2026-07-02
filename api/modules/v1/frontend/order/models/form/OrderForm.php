<?php

namespace api\modules\v1\frontend\order\models\form;

use Yii;
use Exception;
use api\modules\v1\admin\inventory\models\ProductVariant;
use api\modules\v1\frontend\pos\models\OrderItem;
use common\models\DiscountCode;
use api\helper\response\ResponseBuilder;
use common\models\Order;
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

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [["carts", "total_price"], "required"],
            [["email"], "email"],
            [["discount", "status"], 'default', 'value' => 0],
            ["price_policy", 'default', 'value' => Order::UNIT_PRICE],
            ["type", 'default', 'value' => Order::TYPE_ORDER_NORMAL],
            ["channel", 'default', 'value' => Order::CHANEL_WEBSITE],
            ["carts", IsArrayValidator::class],
            ["carts", "checkCartRule"],
        ]);

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
        $this->payments = strval($this->total_price - $this->discount);
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


}
