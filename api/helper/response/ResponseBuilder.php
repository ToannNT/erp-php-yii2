<?php
namespace api\helper\response;

use Yii;
use yii\rest\Serializer;
use yii\data\DataProviderInterface;
use yii\web\HttpException;

class ResponseBuilder
{

    /**
     * @param bool $status
     * @param mixed|null $data
     * @param string|null $message
     * @param int $code
     * @return array
     */
    public static function responseJson(bool $status = true, $data = null, string $message = "", int $code = 200): array
    {
        Yii::$app->response->statusCode = $code;
        if ($data instanceof DataProviderInterface) {
            $serializer = new Serializer(['collectionEnvelope' => 'items']);
            $data = $serializer->serialize($data);
        }
        return [
            "status" => $status,
            "data" => $data,
            "messages" => $message,
            "code" => $code
        ];
    }
}