<?php

namespace api\trails;

use Exception;

trait ErrorTrait
{
    protected $errors;

    /**
     * @param $key
     * @param $errors
     * @return mixed
     * @throws Exception
     */
    public function setErrors($key, $errors)
    {
        $this->errors = [
            $key => is_string($errors) ? [$errors] : $errors
        ];
        throw new  Exception(json_encode($errors));
    }

    public function getErrors()
    {
        return $this->errors;
    }
}