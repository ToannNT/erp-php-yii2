<?php

namespace common\models;

use common\models\base\OrderShip as BaseOrderShip;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_ship".
 */
class OrderShip extends BaseOrderShip
{
    const TRANSPORT_FLY = 1;
    const TRANSPORT_ROAD = 2;
    const PAYMENT_TYPE_SENDER = "NGUOIGUI";
    const PAYMENT_TYPE_ID_SENDER = 1;
    const PAYMENT_TYPE_ID_RECEIVER = 2;
    const PAYMENT_TYPE_RECEIVER = "NGUOINHAN";
    const WEIGHT_GRAM = "gram";
    const WEIGHT_KG = "kg";
    const STATUS_CREATED = 0;
    const STATUS_CANCEL_BILL_LADING = -1;
    const STATUS_TO_SHIP = 1;
    const STATUS_TO_RECEIVE = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_RETURNED = 5;
    const INSURANCE_VALUE = 5000;

    public $mapPaymentType = [
        self::PAYMENT_TYPE_SENDER => self::PAYMENT_TYPE_ID_SENDER,
        self::PAYMENT_TYPE_RECEIVER => self::PAYMENT_TYPE_ID_RECEIVER
    ];

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
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

    public function serviceExtras(): array
    {
        return [
            [
                "code" => "value",
                "title" => "Khai Giá",
                "view_type" => "checkbox_with_value",
            ],
            [
                "code" => "coupon",
                "title" => "Mã Giảm Giá",
                "view_type" => "text"
            ],
            [
                "code" => "payment_type",
                "view_type" => "radio",
                "data" => [
                    [
                        "title" => "NGƯỜI GỬI TRẢ PHÍ",
                        "value" => self::PAYMENT_TYPE_SENDER
                    ],
                    [
                        "title" => "NGƯỜI NHẬN TRẢ PHÍ",
                        "value" => self::PAYMENT_TYPE_RECEIVER
                    ]
                ],
                "value" => self::PAYMENT_TYPE_RECEIVER
            ],
            [
                "code" => "shipper_note",
                "view_type" => "dropdown",
                "data" => [
                    [
                        "title" => "Không cho xem hàng",
                        "value" => "KHONGCHOXEMHANG"
                    ],
                    [
                        "title" => "Cho xem khong thu",
                        "value" => "CHOXEMHANGKHONGTHU"
                    ],
                    [
                        "title" => "Cho thu",
                        "value" => "CHOTHUHANG"
                    ]
                ],
                "value" => "KHONGCHOXEMHANG"
            ],
            [
                "code" => "tag_note",
                "view_type" => "dropdown",
                "data" => [
                    [
                        "title" => "Dễ Vỡ",
                        "value" => "7"
                    ],
                    [
                        "title" => "Nông Sản",
                        "value" => "1"
                    ]
                ]
            ],
            [
                "code" => "pick_station",
                "view_type" => "checkbox",
                "data" => [
                    [
                        "title" => "Gửi hàng tại điểm nhận",
                        "value" => 1
                    ]
                ]
            ]
        ];
    }

    public function getShipper()
    {
        return $this->hasOne(Shipper::className(), ["id" => "shipper_id"]);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ["id" => "order_id"]);
    }

    public function cancelBillLading(): bool
    {
        $this->extra_fields = null;
        $this->extra_shipper = null;
        $this->shipper_id = null;
        $this->shipper_type = null;
        $this->expected_delivery_time = null;
        $this->partner_code = null;
        $this->status = self::STATUS_CANCEL_BILL_LADING;
        return $this->save(false);
    }
}
