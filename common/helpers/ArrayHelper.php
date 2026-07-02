<?php

namespace common\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    public static function inListArray($data, $needles, $strict = false)
    {
        foreach ($data as $item) {
            if (empty($item[key($needles)])) {
                return false;
            }
            if ($strict ? $item[key($needles)] === $needles[key($needles)] : $item[key($needles)] == $needles[key($needles)]) {
                return true;
            }
        }
        return false;
    }
}