<?php

namespace common\models;

use Yii;
use \common\models\base\Shipper as BaseShipper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shipper".
 */
class Shipper extends BaseShipper
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;

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

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ["id" => "created_by"]);
    }
}
