<?php

namespace common\components\shipping\shipper;

use common\components\shipping\IShipper;
use common\components\shipping\models\ShipperRequestBaseObject;
use common\models\District;
use common\models\OrderShip;
use common\models\Province;
use common\models\Shipper;
use common\models\Ward;
use Exception;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\web\HttpException;

/**
 * @property Shipper $shipperModel
 * @property string $token
 * @property string $shopId
 * @property ShipperRequestBaseObject $shipperRequest
 */
class GHNShipper implements IShipper
{
    const PROVINCE_API = "shiip/public-api/master-data/province";
    const DISTRICT_API = "shiip/public-api/master-data/district";
    const WARD_API = "shiip/public-api/master-data/ward";
    const CALCULATE_DELIVERY_FEE_API = "shiip/public-api/v2/shipping-order/fee";
    const CALCULATE_LEAD_TIME_API = "shiip/public-api/v2/shipping-order/leadtime";
    const CREATE_ORDER_API = "shiip/public-api/v2/shipping-order/create";
    const GEN_TOKEN_PRINT_BILL_API = "shiip/public-api/v2/a5/gen-token";
    const CANCEL_ORDER_API = "shiip/public-api/v2/switch-status/cancel";
    const PRINT_BILL_API = "a5/public-api";
    const SHIPPER = "Giao Hàng Nhanh";
    public const TYPE = "ghn";
    const FAST_DELIVERY_TRANSPORT = 53319;
    const NORMAL_DELIVERY_TRANSPORT = 53321;
    const STANDARD_DELIVERY_TRANSPORT = 53320;
    const PAYMENT_TYPE_SENDER = 1;
    const PAYMENT_TYPE_RECEIVER = 2;
    protected $token;
    protected $shopId;
    protected $errors;
    /**
     * @var ShipperRequestBaseObject $shipperRequest
     */
    public $shipperRequest = null;
    /**
     * @var Shipper $shipperMode
     */
    protected $shipperModel;

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function getDetail()
    {

    }

    public function getBaseUrl()
    {
        return env("BASE_URL_GHN");
    }

    /**
     * @throws \yii\httpclient\Exception
     */
    public function getPrintDetail()
    {
        $resultToken = $this->fetch(self::GEN_TOKEN_PRINT_BILL_API, [
            "order_codes" => [$this->shipperRequest->order_ship->partner_code],
        ], "POST");
        if (empty($resultToken->data->token)) {
            return false;
        }
        $client = new Client(["baseUrl" => $this->getBaseUrl()]);
        $request = $client->get(self::PRINT_BILL_API . "/printA5", json_encode([
            "order_codes" => [$this->shipperRequest->order_ship->partner_code],
            "token" => $resultToken->data->token
        ]), [
            "Content-Type" => "application/json"
        ], ["timeout" => 5]);
        $response = $request->send($request);
        return $response->getContent();
    }

    public function cancel()
    {
        return $this->fetch(self::CANCEL_ORDER_API, [
            "order_codes" => [$this->shipperRequest->order_ship->partner_code]
        ], "POST");
    }

    public function createOrder()
    {
        $result = $this->fetch(self::CREATE_ORDER_API, $this->shipperRequest->getParamCreateOrders(), "POST");
        $response = [
            "id" => $this->shipperModel->id,
            "shipper" => self::SHIPPER,
            "shipper_type" => self::TYPE
        ];
        if ($result) {
            return array_merge($response, [
                "result" => true,
                "partner_code" => $result->data->order_code,
                "status" => OrderShip::STATUS_CREATED,
                "extra_fields" => json_encode($result),
                "service_extras" => json_encode($this->shipperRequest->service_extras),
                "expected_delivery_time" => date("Y-m-d H:i:s", strtotime($result->data->expected_delivery_time))
            ]);
        }
        return array_merge($response, [
            "result" => false,
            "errors" => $this->getErrors()
        ]);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function calculateShippingFee()
    {
        $result = $this->fetch(self::CALCULATE_DELIVERY_FEE_API, $this->shipperRequest->getParamCalculatorFees(), "POST");
        $response = [
            "id" => $this->shipperModel->id,
            "thumbnail" => $this->shipperModel->thumbnail,
            "shipper" => self::SHIPPER,
            "shipper_type" => self::TYPE,
            "service_extras" => $this->shipperRequest->service_extras
        ];
        if (!$result) {
            return array_merge([
                "status" => false,
                "errors" => $this->getErrors(),
            ], $response);
        }
        $leadTime = null;
        $resultLeadTime = $this->fetch(self::CALCULATE_LEAD_TIME_API, $this->shipperRequest->getParamCalculatorFees(), "GET");
        if (!empty($resultLeadTime->data)) {
            $timeLeft = $resultLeadTime->data->leadtime - time();
            $timeLeft = ceil($timeLeft / 86400);
            $leadTime = "Khoảng $timeLeft ngày";
        }
        return array_merge([
            "status" => true,
            "lead_time" => $leadTime,
            "data" => [
                "total_fee" => $result->data->total,
                "ship_fee" => $result->data->service_fee,
                "insurance_fee" => $result->data->insurance_fee,
                "vat" => 0,
                "delivery_type" => 2,
                "is_support" => true,
                "extra_field" => $result->data,
            ],
        ], $response);
    }

    /**
     * @throws Exception
     */
    public function init(ShipperRequestBaseObject $shipperRequestObject)
    {
        $this->shipperRequest = $shipperRequestObject;
        $this->setShipperModel();
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
        $this->setToken(env("SHIPPER_GHN_TOKEN"));
        $serviceExtras = json_decode($this->shipperModel->service_extras);
        $this->setShopId($serviceExtras->shop_id ?? null);
    }

    public function fetch($url, $data = [], $type = "GET")
    {
        $options = [
            "timeout" => 5
        ];
        $client = new Client(["baseUrl" => $this->getBaseUrl()]);
        $request = $client->get($url, json_encode($data), [
            "Token" => $this->token,
            "ShopId" => $this->shopId,
            "Content-Type" => "application/json"
        ], $options);
        switch ($type) {
            case "POST":
            {
                $request = $client->post($url, json_encode($data), [
                    "Token" => $this->token,
                    "ShopId" => $this->shopId,
                    "Content-Type" => "application/json"
                ], $options);
                break;
            }
        }
        try {
            $response = $client->send($request);
            $resultJson = json_decode($response->getContent());
            if (!$response->isOk) {
//                if (str_contains($resultJson->message, "corev2_tenant_order_calculate_fee")) {
                $this->errors = [$resultJson->message];
//                }
                return false;
            }
            return $resultJson;
        } catch (Exception $exception) {
            $this->errors = ["Error Unknown Timeout"];
            return false;
        }
    }

    public function setShopId($shopId): string
    {
        return $this->shopId = $shopId;
    }

    /**
     * @throws InvalidConfigException|HttpException
     */
    public function synchronizedProvince(): bool
    {
        $result = $this->initSynchronized(self::PROVINCE_API);
        foreach ($result->data as $key => $item) {
            $conditions = ['or'];
            foreach ($item->NameExtension as $name) {
                $conditions[] = ["like", "full_name", $name];
            }
            Province::updateAll(["code_ghn" => $item->ProvinceID], $conditions);
        }
        return true;
    }

    /**
     * @throws InvalidConfigException
     * @throws HttpException|Exception
     */
    public function initSynchronized($url, $data = [])
    {
        $client = new Client();
        $client->baseUrl = $this->getBaseUrl();
        $request = $client->get($url, $data, [
            "token" => $this->token
        ]);
        $response = $client->send($request);
        if (!$response->isOk) {
            throw new HttpException($response->getContent());
        }
        return json_decode($response->getContent());
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException|HttpException
     */
    public function synchronizedDistrict(): bool
    {
        $result = $this->initSynchronized(self::DISTRICT_API);
        foreach ($result->data as $item) {
            if (!empty($item->NameExtension[0])) {
                $conditions = ['or'];
                foreach ($item->NameExtension as $name) {
                    $conditions[] = ["like", "full_name", $name];
                }
                District::updateAll(["code_ghn" => $item->DistrictID], $conditions);
            }
        }
        return true;
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function synchronizedWard()
    {
        $districts = District::find()->all();
        foreach ($districts as $district) {
            try {
                $result = $this->initSynchronized(self::WARD_API, [
                    "district_id" => $district->code_ghn
                ]);
                if (empty($result->data)) {
                    var_dump($district->code_ghn, $result);
                    continue;
                }
            } catch (HttpException $exception) {
                continue;
            }
            foreach ($result->data as $item) {
                if (!empty($item->NameExtension[0])) {
                    $conditions = ['or'];
                    foreach ($item->NameExtension as $name) {
                        $conditions[] = ["like", "full_name", $name];
                    }
                    Ward::updateAll(["code_ghn" => $item->WardCode], ["and", $conditions, ["district_code" => $district->code]]);
                }
            }
        }
    }

}