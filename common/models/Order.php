<?php

namespace common\models;

use api\modules\v1\admin\order\models\Promotion;
use common\behaviors\JsonBehavior;
use common\helpers\ArrayHelper;
use common\models\Promotion as PromotionAlias;
use Exception;
use SamIT\Yii2\Components\Map;
use Yii;
use \common\models\base\Order as BaseOrder;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\Json;

/**
 * Class Order
 * @property Office $office
 * @property Inventory $inventory
 * @property Customer $client
 * @property User $createdBy
 * @property OrderItem[] $orderItems
 * @property string $statusHtml
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Order extends BaseOrder
{
    const STATUS_ORDER = 0;
    const STATUS_APPROVED = 1;
    const STATUS_PACKING = 2;
    const STATUS_STOCK_OUT = 3;
    const STATUS_DONE = 4;
    const STATUS_CANCEL = -1;
    const STATUS_WATING_SHIPPER = 6;
    const STATUS_DELETE = -99;
    const STATUS_RETURN = 5;
    const CHANNEL_POS = "pos";
    const CHANEL_WEBSITE = "website";
    const UNIT_PRICE = 'unit_price';
    const SLL_PRICE = 'sll_price';
    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_PRICE = 2;
    const TYPE_ORDER_NORMAL = 1;
    const TYPE_ORDER_SHIPPER = 2;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["data_tax", "data_delivery_fee", "shipping_address", "order_address", "tags", "progress_status", "data_discount", "data_payments", "data_other_fee"]
        ];
        return $behaviors;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $this->code = 'OR' . str_pad($this->id, 7, "0", STR_PAD_LEFT);
            $this->updateAttributes(['code' => $this->code]);
        }
    }

    public function getPaymentMethods()
    {
        return $this->hasMany(PaymentMethod::className(), ["id" => "payment_method_id"])
            ->via("orderPaymentMethods");
    }

    public function getOrderPaymentMethods()
    {
        return $this->hasMany(OrderPaymentMethod::className(), ["order_id" => "id"]);
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

    public function getObjectPaymentMethods()
    {
        $paymentMethods = [];
        foreach ($this->orderPaymentMethods as $orderPaymentMethod) {
            $paymentMethods[$orderPaymentMethod->paymentMethod->code] = [
                "id" => $orderPaymentMethod->paymentMethod->id,
                "name" => $orderPaymentMethod->paymentMethod->name,
                "code" => $orderPaymentMethod->paymentMethod->code,
                "payment" => $orderPaymentMethod->payment
            ];
        }
        return $paymentMethods;
    }

    public function getMapOrderPaymentMethods()
    {
        return array_map(function ($orderPaymentMethod) {
            return [
                "id" => $orderPaymentMethod->paymentMethod->id,
                "code" => $orderPaymentMethod->paymentMethod->code,
                "name" => $orderPaymentMethod->paymentMethod->name,
                "payment" => $orderPaymentMethod->payment
            ];
        }, $this->orderPaymentMethods);
    }


    public function attributeLabels()
    {
        return [
            "price_policy" => Yii::t("api", "Price Policy"),
            "tax" => Yii::t("api", "Tax")
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
    public function getInventory()
    {
        return $this->hasOne(Inventory::class, ['id' => 'inventory_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Customer::class, ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getOrderReturns()
    {
        return $this->hasMany(OrderReturn::className(), ["order_id" => "id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public function getOrderShip()
    {
        return $this->hasOne(OrderShip::class, ["order_id" => "id"]);
    }

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'OR' . $tmp;
    }

    public function getPromotion()
    {
        return $this->hasOne(Promotion::class, ["id" => "promotion_id"]);
    }

    public function getPromotions()
    {
        return $this->hasMany(OrderDiscount::class, ["order_id" => "id"]);
    }

    public function deleteAllOrderItems()
    {
        OrderItem::deleteAll(["order_id" => $this->id]);
    }

    //    public function addPromotion($params): bool
    //    {
    //        $dataDiscount = [];
    //        $dynamicModel = new RecordModel([
    //            "promotion_code",
    //            "discount_value",
    //            "discount_type",
    //        ]);
    //        $that = $this;
    //        $dynamicModel->addRule("promotion_code", function () use ($that, &$dataDiscount) {
    //            if (!$this->promotion_code) {
    //                return false;
    //            }
    //            $promotion = PromotionAlias::find()->where(["code" => $this->promotion_code])->available()->one();
    //            if (!$promotion) {
    //                $this->addError("promotion", "Promotion not found");
    //                return false;
    //            } else {
    //                $offices = json_decode($promotion->offices, true);
    //                if ($offices && !$that->searchKeyObject($offices, ["id" => $that->office_id])) {
    //                    $this->addError("office", Yii::t("api", "Does not apply to this office"));
    //                    return false;
    //                }
    //                if ($promotion->order_total_required > $that->payments) {
    //                    $this->addError("order_total_required", Yii::t("api", "Payment less than Order total required"));
    //                    return false;
    //                }
    //                $dataDiscount["promotion_data"] = [
    //                    "order_total_required" => $promotion->order_total_required,
    //                    "offices" => $promotion->offices,
    //                    "discount_value" => $promotion->discount_value,
    //                    "discount_type" => $promotion->discount_type,
    //                    "condition_type" => $promotion->condition_type,
    //                    "condition_items" => $promotion->condition_items
    //                ];
    //                $that->promotion_id = $promotion->id;
    //            }
    //        });
    //        $dynamicModel->addRule(["discount_value"], "required", [
    //            "when" => function ($model) {
    //                return $model->discount_type;
    //            }
    //        ]);
    //        $dynamicModel->addRule(["discount_type"], "safe");
    //        $dynamicModel->load($params, $this->formName());
    //        if (!empty($dynamicModel->discount_value)) {
    //            $dataDiscount = array_merge($dataDiscount, [
    //                "discount_value" => $dynamicModel->discount_value,
    //                "discount_type" => $dynamicModel->discount_type
    //            ]);
    //        }
    //        if ($dynamicModel->validate()) {
    //            $this->data_discount = $dataDiscount;
    //            return true;
    //        }
    //        $this->addErrors($dynamicModel->getErrorSummary(true));
    //        return false;
    //    }

    /**
     * @throws \yii\db\Exception
     */
    public function addMultiplePromotion($codes)
    {
        $dataPromotion = [];
        $transaction = Yii::$app->db->beginTransaction();
        foreach ((array)$codes as $code) {
            $promotion = Promotion::find()->where(["code" => $code])->active()->one();
            if (!$promotion) {
                $transaction->rollBack();
                $this->addError("code", "promotion code {code} invalid", [
                    "code" => 32344
                ]);
                return false;
            }
            $offices = json_decode($promotion->offices, true);
            if ($offices && !ArrayHelper::inListArray($offices, ["id" => Yii::$app->user->identity->office->id])) {
                $transaction->rollBack();
                $this->addError("office", Yii::t("api", "{code} Does not apply to this office"), [
                    "code" => "3"
                ]);
                return false;
            }
            if ($promotion->order_total_required > $this->getSumSubTotalOrderItems()) {
                $transaction->rollBack();
                $this->addError("order_total_required", Yii::t("api", "Payment less than Order total required"));
                return false;
            }

            $orderDiscount = new OrderDiscount([
                "order_id" => $this->id,
                "title" => $promotion->title,
                "type_id" => $promotion->id,
                "type" => OrderDiscount::TYPE_PROMOTION,
                "code" => $promotion->code,
                "discount_type" => $promotion->discount_type,
                "discount_value" => $promotion->discount_value,
                "offices" => $promotion->offices,
                "condition_items" => $promotion->condition_items,
                "condition_type" => $promotion->condition_type,
                "apply_for_all" => $promotion->apply_for_all,
                "order_total_required" => $promotion->order_total_required,
                "limit" => $promotion->limit,
                "used" => $promotion->used,
                "extras_fields" => $promotion->getAttributes()
            ]);
            $orderDiscount->save(false);
        }
        $transaction->commit();
        return $dataPromotion;
    }

    //    public function calculateDiscount()
    //    {
    //        $dataDiscount = $this->data_discount;
    //
    //        if (!is_array($dataDiscount) && !$dataDiscount instanceof Map) {
    //            return false;
    //        }
    //        $this->discount = 0;
    //        // handle discount
    //        if (isset($dataDiscount["discount_value"]) && isset($dataDiscount["discount_type"])) {
    //            if ($dataDiscount["discount_type"] == self::DISCOUNT_PERCENT) {
    //                $this->discount += $this->total_price * ($dataDiscount["discount_value"] / 100);
    //            } else {
    //                $this->discount += $dataDiscount["discount_value"];
    //            }
    //        }
    //        // handle promotion
    //        $promotion = Promotion::find()->where(["id" => $this->promotion_id])->available()->one();
    //        if (!isset($dataDiscount["promotion_data"]) || !$promotion) {
    //            $this->promotion_id = null;
    //            return true;
    //        }
    //        $dataPromotion = $dataDiscount["promotion_data"];
    //        $offices = json_decode($dataPromotion["offices"], true);
    //        if ($offices && !$this->searchKeyObject($offices, ["id" => $this->office_id])) {
    //            $this->promotion_id = null;
    //            return false;
    //        }
    //        if ($promotion->order_total_required != null &&
    //            $promotion->order_total_required > $this->total_price
    //        ) {
    //            $this->promotion_id = null;
    //            return false;
    //        }
    //        switch ($promotion->condition_type) {
    //            case PromotionAlias::PROMOTION_ORDER :
    //            {
    //                if ($promotion->discount_type == self::DISCOUNT_PERCENT) {
    //                    $this->discount += $this->total_price * ($promotion->discount_value) / 100;
    //                } else {
    //                    $this->discount += $promotion->discount_value;
    //                }
    //                break;
    //            }
    //            case PromotionAlias::PROMOTION_PRODUCT:
    //            {
    //                $is_discount = false;
    //                foreach ($this->orderItems as $item) {
    //                    // recalculate discount price
    //                    $item->discount_price = 0;
    //                    $variantId = $item["product_variant_id"];
    //                    if ($promotion->apply_for_all) {
    //                        $is_discount = true;
    //                        if ($promotion->discount_type == self::DISCOUNT_PERCENT) {
    //                            $item->discount_price += $item->unit_price * ($promotion->discount_value / 100);
    //                        } else {
    //                            $item->discount_price += $promotion->discount_value;
    //                        }
    //                    } else if ($this->searchKeyObject(json_decode($dataPromotion["condition_items"], true), ["itemId" => $variantId])) {
    //                        $is_discount = true;
    //                        if ($promotion->discount_type === self::DISCOUNT_PERCENT) {
    //                            $item->discount_price += $item->unit_price * ($promotion->discount_value / 100);
    //                        } else {
    //                            $item->discount_price += $promotion->discount_value;
    //                        }
    //                    }
    //                    $item->calculate();
    //                    $item->save(false);
    //                }
    //                if (!$is_discount) {
    //                    $this->promotion_id = null;
    //                }
    //                $this->calculateSubTotal();
    //                break;
    //            }
    //            case PromotionAlias::PROMOTION_CATEGORY:
    //            {
    //                $is_discount = false;
    //                foreach ($this->orderItems as $item) {
    //                    // recalculate discount price
    //                    $item->discount_price = 0;
    //                    $product = Product::find()->where(["id" => $item->product_id])->one();
    //                    if ($promotion->apply_for_all) {
    //                        if ($promotion->discount_type == self::DISCOUNT_PERCENT) {
    //                            $item->discount_price += $item->unit_price * ($promotion->discount_value / 100);
    //                        } else {
    //                            $item->discount_price += $promotion->discount_value;
    //                        }
    //                        $is_discount = true;
    //                    } else if ($this->searchKeyObject(json_decode($dataPromotion["condition_items"], true), ["itemId" => $product->category_id])) {
    //                        $is_discount = true;
    //                        if ($promotion->discount_type == self::DISCOUNT_PERCENT) {
    //                            $item->discount_price += $item->unit_price * ($promotion->discount_value / 100);
    //                        } else {
    //                            $item->discount_price += $promotion->discount_value;
    //                        }
    //                    }
    //                    $item->calculate();
    //                    $item->save(false);
    //                }
    //                if (!$is_discount) {
    //                    $this->promotion_id = null;
    //                }
    //                break;
    //            }
    //            case PromotionAlias::PROMOTION_SUPPLIER:
    //            {
    //                $is_discount = false;
    //                /**
    //                 * @var $orderItem OrderItem
    //                 * @var $supplier Supplier
    //                 */
    //                $orderItems = $this->orderItems;
    //                if ($promotion->apply_for_all) {
    //                    foreach ($orderItems as $orderItem) {
    //                        foreach ($orderItem->suppliers as $supplier) {
    //                            if ($this->searchKeyObject(json_decode($dataPromotion["condition_items"], true), ["itemId" => $supplier->id])) {
    //                                $is_discount = true;
    //                                break 2;
    //                            }
    //                        }
    //                    }
    //                } else {
    //                    foreach ($orderItems as $orderItem) {
    //                        $productVariant = $orderItem->productVariant;
    //                        if ($this->searchKeyObject(json_decode($dataPromotion["condition_items"], true), ["itemId" => $productVariant->id])) {
    //                            $is_discount = true;
    //                            break;
    //                        }
    //                    }
    //                }
    //                if ($is_discount) {
    //                    if ($promotion->discount_type == self::DISCOUNT_PERCENT) {
    //                        $this->discount += $this->total_price * ($promotion->discount_value) / 100;
    //                    } else {
    //                        $this->discount += $promotion->discount_value;
    //                    }
    //                } else {
    //                    $this->promotion_id = null;
    //                }
    //                break;
    //            }
    //        }
    //    }

    public function calculateDiscount()
    {
        $orderDiscounts = OrderDiscount::find()->where(["order_id" => $this->id])->orderBy(["discount_type" => SORT_DESC])->all();
        foreach ($orderDiscounts as $orderDiscount) {
            if ($orderDiscount->order_total_required > $this->getSumSubTotalOrderItems()) {
                continue;
            }
            switch ($orderDiscount->condition_type) {
                case PromotionAlias::PROMOTION_ORDER:
                    $this->calculatePromotionOrder($orderDiscount);
                    break;
                case PromotionAlias::PROMOTION_PRODUCT:
                    $this->calculatePromotionProduct($orderDiscount);
                    break;
                case PromotionAlias::PROMOTION_CATEGORY:
                    $this->calculatePromotionCategory($orderDiscount);
                    break;
                case PromotionAlias::PROMOTION_SUPPLIER:
                    if ($orderDiscount->discount_type === PromotionAlias::DISCOUNT_SAME_PRICE) {
                        $this->calculatePromotionSupplierSamePrice($orderDiscount);
                        break;
                    }
                    $this->calculatePromotionSupplier($orderDiscount);
            }
            $orderDiscount->save(false);
        }
    }

    public function clearDiscountPromotion()
    {
        $this->discount = 0;
        foreach ($this->orderItems as $orderItem) {
            $orderItem->discount_price = 0;
            $orderItem->calculate();
            $orderItem->save(false);
        }
    }

    protected function calculatePromotionOrder(OrderDiscount $orderDiscount)
    {
        if ($orderDiscount->discount_type === self::DISCOUNT_PERCENT) {
            $discount = $this->total_price * ($orderDiscount["discount_value"]) / 100;
        } else {
            $discount = $this->total_price === 0 ? 0 : $orderDiscount["discount_value"];
        }
        $this->discount += $discount;
        $orderDiscount->discount_price = $discount;
    }

    protected function calculatePromotionProduct(OrderDiscount $orderDiscount)
    {
        $totalDiscountPromotion = 0;
        $count = 0;
        foreach ($this->orderItems as $item) {
            // recalculate discount price
            $variantId = $item["product_variant_id"];
            if ($orderDiscount->apply_for_all) {
                $discountPrice = $this->calculateDiscountOrderItem($orderDiscount, $item);
            } else {
                if (ArrayHelper::inListArray($orderDiscount->condition_items, ["itemId" => $variantId])) {
                    $discountPrice = $this->calculateDiscountOrderItem($orderDiscount, $item);
                } else {
                    continue;
                }
            }
            $count += $item->quantity;
            if ($item->discount_price > 0) {
                continue;
            }
            $totalDiscountPromotion += $discountPrice * $item->quantity;
            $item->discount_price += $discountPrice;
            $item->calculate();
            $item->save(false);
        }
        $this->calculateSubTotal();
        $orderDiscount->discount_price = $totalDiscountPromotion;
    }

    protected function calculateDiscountOrderItem(OrderDiscount $orderDiscount, OrderItem $item)
    {
        if ($orderDiscount->discount_type === self::DISCOUNT_PERCENT) {
            return $item->unit_price * ($orderDiscount->discount_value / 100);
        } elseif ($orderDiscount->discount_type === PromotionAlias::DISCOUNT_SAME_PRICE) {
            return $item->unit_price - $orderDiscount->discount_value;
        }
        return $orderDiscount->discount_value;
    }

    protected function calculatePromotionCategory(OrderDiscount $orderDiscount)
    {
        $count = 0;
        $totalDiscountPromotion = 0;
        foreach ($this->orderItems as $item) {
            // recalculate discount price
            $product = Product::find()->where(["id" => $item->product_id])->one();
            if ($orderDiscount["apply_for_all"]) {
                $discountPrice = $this->calculateDiscountOrderItem($orderDiscount, $item);
            } else {
                if (ArrayHelper::inListArray($orderDiscount["condition_items"], ["itemId" => $product->category_id])) {
                    $discountPrice = $this->calculateDiscountOrderItem($orderDiscount, $item);
                } else {
                    continue;
                }
            }
            $count += $item->quantity;
            if ($item->discount_price > 0) {
                continue;
            }
            $totalDiscountPromotion += $discountPrice * $item->quantity;
            $item->discount_price += $discountPrice;
            $item->calculate();
            $item->save(false);
        }
        $this->calculateSubTotal();
        $orderDiscount->discount_price = $totalDiscountPromotion;
    }

    protected function calculatePromotionSupplierSamePrice(OrderDiscount $orderDiscount)
    {
        $orderItems = $this->orderItems;
        $count = 0;
        $totalDiscountPromotion = 0;
        foreach ($orderItems as $orderItem) {
            foreach ($orderItem->suppliers as $supplier) {
                if (ArrayHelper::inListArray($orderDiscount["condition_items"], ["itemId" => $supplier->id])) {
                    if ($orderItem->discount_price <= 0) {
                        $count += $orderItem->quantity;
                        $orderItem->discount_price = $this->calculateDiscountOrderItem($orderDiscount, $orderItem);
                        $totalDiscountPromotion += $orderItem->discount_price * $orderItem->quantity;
                    }
                    break 1;
                }
            }
            $orderItem->calculate();
            $orderItem->save(false);
        }
        $this->calculateSubTotal();
        $orderDiscount->discount_price = $totalDiscountPromotion;
    }

    protected function calculatePromotionSupplier(OrderDiscount $orderDiscount)
    {
        /**
         * @var $orderItem OrderItem
         * @var $supplier Supplier
         */
        $discount = 0;
        $is_discount = false;
        $orderItems = $this->orderItems;
        if ($orderDiscount["apply_for_all"]) {
            foreach ($orderItems as $orderItem) {
                foreach ($orderItem->suppliers as $supplier) {
                    if (ArrayHelper::inListArray($orderDiscount["condition_items"], ["itemId" => $supplier->id])) {
                        $is_discount = true;
                        break 2;
                    }
                }
            }
        } else {
            foreach ($orderItems as $orderItem) {
                $productVariant = $orderItem->productVariant;
                if (ArrayHelper::inListArray($orderDiscount["condition_items"], ["itemId" => $productVariant->id])) {
                    $is_discount = true;
                    break;
                }
            }
        }
        if ($is_discount) {
            if ($orderDiscount->discount_type === self::DISCOUNT_PERCENT) {
                $discount = $this->total_price * ($orderDiscount->discount_value) / 100;
            } else {
                $discount = $orderDiscount->discount_value;
            }
            $this->discount += $discount;
            $orderDiscount->discount_price = $this->discount;
        }
    }

    public function resetDiscount()
    {
        $this->discount = $this->discount < 0 ? 0 : $this->discount;
    }

    public function resetPayments()
    {
        $this->payments = $this->payments < 0 ? 0 : $this->payments;
    }

    public function calculateDeliveryFee()
    {
        $this->delivery_fee = 0;
        if (!$this->data_delivery_fee || !is_array($this->data_delivery_fee)) {
            return false;
        }
        $deliveryFeeValue = $this->data_delivery_fee["delivery_fee_value"];
        if (!empty($this->data_delivery_fee["delivery_fee_id"])) {
            $deliveryFee = DeliveryFee::find()
                ->where(["id" => $this->data_delivery_fee["delivery_fee_id"]])
                ->active()
                ->one();
            $deliveryFeeValue = $deliveryFee->price ?? 0;
        }
        $this->delivery_fee = $deliveryFeeValue;
    }

    /**
     * format data_other_fee
     * [
     *  {
     *   "name":"insurance",
     *   "value":10000
     *  }
     * ]
     * @return bool|false
     */

    public function calculateOtherFee()
    {
        $this->other_fee = 0;
        if (!$this->data_other_fee || !is_array($this->data_other_fee)) {
            return false;
        }
        foreach ($this->data_other_fee as $item) {
            $this->other_fee += $item["value"] ?? 0;
        }
        return $this->other_fee;
    }

    public function calculateTax()
    {
        if (!is_array($this->data_tax) || empty($this->data_tax["tax_value"])) {
            return false;
        }
        $this->tax_price = ($this->total_price - $this->discount) * $this->data_tax["tax_value"] / 100;
    }

    public function calculateSubTotal()
    {
        $this->total_price = 0;
        $this->quantity = 0;
        foreach ($this->orderItems as $orderItem) {
            $this->total_price += $orderItem->total_price;
            $this->quantity += $orderItem->quantity;
        }
    }

    public function calculatePayment()
    {
        $this->payments = $this->total_price + $this->tax_price + $this->delivery_fee + $this->other_fee + -$this->discount;
    }

    public function calculate()
    {
        $this->clearDiscountPromotion();
        $this->calculateSubTotal();
        $this->calculateDiscount();
        $this->resetDiscount();
        $this->calculateDeliveryFee();
        $this->calculateOtherFee();
        $this->calculateTax();
        $this->calculatePayment();
        $this->resetPayments();
    }


    public function addUsedPromotion()
    {
        foreach ($this->promotions as $orderDiscount) {
            $promotion = Promotion::find()->where(["id" => $orderDiscount->type_id])->one();
            if ($orderDiscount->discount_price == 0 || !$promotion) {
                return false;
            }
            $promotion->used++;
            if (!$promotion->save(false)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $payment_methods
     *  [
     *    [
     *      "payment_method_id" => 1,
     *      "payment_method_name" => "Tiền Mặt",
     *      "payment" => 10000
     *    ]
     *  ]
     * total payment must equal payments
     * @throws Exception
     */

    public function savePaymentMethods(array $payment_methods)
    {
        foreach ($payment_methods as $payment_method) {
            $status = (new OrderPaymentMethod([
                "payment_method_id" => $payment_method["payment_method_id"],
                "payment" => $payment_method["payment"],
                "order_id" => $this->id
            ]))->save(false);
            if (!$status) {
                throw new Exception($status);
            }
        }
    }

    public function removeUsedPromotion()
    {
    }

    public function getInventoryIssues()
    {
        return $this->hasMany(InventoryIssue::className(), ["order_id" => "id"]);
    }

    /**
     * @throws Exception
     */
    public function calculateCancel()
    {
        $inventoryIssues = $this->inventoryIssues;
        /**
         * @var InventoryIssue $inventoryIssue
         */
        foreach ($inventoryIssues as $inventoryIssue) {
            /**
             * @var InventoryIssueItem $inventoryIssueItems
             * @var InventoryIssue $inventoryIssue
             */
            foreach ($inventoryIssue->inventoryIssueItems as $inventoryIssueItem) {
                $productInventory = ProductInventory::interactive($inventoryIssue, $inventoryIssueItem)
                    ->addAvailable($inventoryIssueItem->quantity);
                if (!$productInventory->save()) {
                    throw new Exception("Can't save Product Inventory");
                }
                InventoryHistory::create([
                    "action" => InventoryHistory::ACTION_CANCEL_ORDER,
                    "change_quantity" => "+$inventoryIssueItem->quantity",
                    "type" => InventoryHistory::TYPE_INVENTORY_ISSUE,
                    "inventory" => $productInventory->available,
                    "created_by" => Yii::$app->user->getId(),
                    "inventory_id" => $inventoryIssue->inventory_id,
                    "product_id" => $inventoryIssueItem->product_id,
                    "product_variant_id" => $inventoryIssueItem->product_variant_id,
                    "voucher_code" => $inventoryIssue->code
                ]);
            }
            $inventoryIssue->status = InventoryIssue::STATUS_CANCEL;
            if (!$inventoryIssue->save(false)) {
                throw new Exception("Can't cancel Inventory Issue");
            }
        }
    }

    public function addProgressStatus($status)
    {
        $progress_status = json_decode(json_encode($this->progress_status), true);
        $progress_status = ArrayHelper::merge($progress_status ?: [], [
            [
                "status" => $status,
                "date" => date("Y-m-d H:i:s")
            ]
        ]);
        $this->progress_status = $progress_status;
    }

    public function getSumDiscountPriceOrderItems()
    {
        return $this->hasMany(OrderItem::class, ["order_id" => "id"])->sum("order_item.discount_price");
    }

    public function getSumSubTotalOrderItems()
    {
        return $this->hasMany(OrderItem::class, ["order_id" => "id"])->sum("order_item.sub_total");
    }
}
