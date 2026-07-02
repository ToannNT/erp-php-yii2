<?php


namespace api\helper\response;

use BadMethodCallException;

class ResponseHelper
{
    /**
     * @param string $status
     * @param string|array|null $data
     * @param string|null $message
     * @param string|array|null $error
     * @param string|null $ok_status
     * @return array
     * @throws BadMethodCallException
     */
    public static function build($status, $data = null, $error = null, $message = null, $ok_status = null)
    {
        if ($status == ApiConstant::STATUS_OK) {
            return ['result' => ResultHelper::build($ok_status, $data, $error, $message), 'error' => null, 'message' => null, 'data' => null, 'status'=>ApiConstant::STATUS_OK];
        } else if ($status == ApiConstant::STATUS_FAIL) {
            return [
                'result' => null,
                'status' => $status,
                'error' => $error,
                'message' => $message
            ];
        }
        throw new BadMethodCallException();
    }
}