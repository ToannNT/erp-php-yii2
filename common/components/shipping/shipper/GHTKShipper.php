<?php

namespace common\components\shipping\shipper;

use common\components\shipping\IShipper;
use common\components\shipping\models\GHTKRequestObject;
use common\components\shipping\models\ShipperRequestBaseObject;
use common\models\OrderShip;
use common\models\Shipper;
use Exception;
use yii\httpclient\Client;

/**
 * @property Shipper $shipperModel
 * @property string $token
 * @property string $shopId
 * @property GHTKRequestObject $shipperRequestObject
 */
class GHTKShipper implements IShipper
{
    public const TYPE = "ghtk";
    protected $shipperModel;
    const USE_RETURN_ADDRESS = 1;
    const NOT_USE_RETURN_ADDRESS = 0;
    const CALCULATE_DELIVERY_FEE_API = "services/shipment/fee";
    const CREATE_ORDER_API = "services/shipment/order";
    const CANCEL_ORDER_API = "services/shipment/cancel";
    const PRINT_BILL_API = "services/label";
    const WEIGHT_GRAM = "gram";
    const WEIGHT_KG = "kilogram";
    const SHIPPER = "Giao Hàng Tiết Kiệm";
    const PAYMENT_TYPE_SENDER = 1;
    const PAYMENT_TYPE_RECEIVER = 0;
    /**
     * @var GHTKRequestObject $shipperRequestObject
     */
    protected $shipperRequestObject;
    protected $token;
    protected $errors;

    /**
     * @throws Exception
     */
    public function init(ShipperRequestBaseObject $shipperRequestObject)
    {
        $this->shipperRequestObject = $shipperRequestObject;
        $this->setShipperModel();
    }

    public function getDetail()
    {
        // TODO: Implement getDetail() method.
    }

    public function getPrintDetail()
    {
        return $this->fetch(self::PRINT_BILL_API . "/" . $this->shipperRequestObject->order_ship->partner_code, null);
    }

    public function cancel()
    {
        return $this->fetch(self::CANCEL_ORDER_API . "/" . $this->shipperRequestObject->order_ship->partner_code, null, "POST");
    }

    public function createOrder()
    {
        $result = $this->fetch(self::CREATE_ORDER_API, $this->shipperRequestObject->paramCreateOrders(), "POST");
        $response = [
            "id" => $this->shipperModel->id,
            "shipper" => self::SHIPPER,
            "shipper_type" => self::TYPE
        ];
        if (!$result) {
            return array_merge($response, [
                "result" => false,
                "errors" => $this->getErrors()
            ]);
        }
        return array_merge($response, [
            "result" => true,
            "partner_code" => $result->order->label,
            "status" => OrderShip::STATUS_CREATED,
            "extra_fields" => json_encode($result),
            "service_extras" => json_encode($this->shipperRequestObject->service_extras),
            "expected_delivery_time" => null
        ]);
    }

    public function calculateShippingFee()
    {
        $result = $this->fetch(self::CALCULATE_DELIVERY_FEE_API, $this->shipperRequestObject->paramsCalculateDelivery());
        $response = [
            "id" => $this->shipperModel->id,
            "shipper" => self::SHIPPER,
            "shipper_type" => self::TYPE,
            "thumbnail" => "https://khachhang.giaohangtietkiem.vn/web/img/icons/apple-touch-icon-152x152.png",
            "service_extras" => $this->shipperRequestObject->service_extras
        ];
        if ($result) {
            return array_merge([
                "status" => true,
                "lead_time" => "Chưa Biết",
                "data" => [
                    "total_fee" => $result->fee->fee,
                    "ship_fee" => $result->fee->ship_fee_only,
                    "insurance_fee" => $result->fee->insurance_fee,
                    "vat" => (float)$result->fee->include_vat,
                    "delivery_type" => $result->fee->delivery_type,
                    "is_support" => $result->fee->delivery,
                    "extra_field" => $result,
                ],
            ], $response);
        }
        return array_merge([
            "status" => false,
            "errors" => $this->getErrors(),
        ], $response);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @throws Exception
     */
    public function setShipperModel()
    {
        $shipper = Shipper::find()->where(["LIKE", "name", self::SHIPPER])->one();
        if (!$shipper) {
            throw new Exception("Can't get shipper model");
        }
        $this->shipperModel = $shipper;
        $this->setToken(env("SHIPPER_GHTK_TOKEN"));
    }

    public function fetch($url, $data = [], $type = "GET")
    {
        $options = [
            "timeout" => 5
        ];
        $client = new Client(["baseUrl" => $this->getBaseUrl()]);
        $request = $client->get($url, $data, [
            "Token" => $this->token
        ], $options);
        switch ($type) {
            case "POST":
            {
                $request = $client->post($url, $data, [
                    "Token" => $this->token
                ], $options);
                break;
            }
        }
        try {
            $response = $client->send($request);
            if (!empty($response->getHeaders()["content-type"]) && $response->getHeaders()["content-type"] == "application/pdf") {
                return $response->getContent();
            }
            $resultJson = json_decode($response->getContent());
            if (!$response->isOk || !$resultJson->success) {
                $this->errors = [$resultJson->message];
                return false;
            }
            return $resultJson;
        } catch (Exception $exception) {
            $this->errors = ["Error Unknown Timeout"];
            return false;
        }

    }

    public function getBaseUrl(): string
    {
        return env("BASE_URL_GHTK");
    }
}