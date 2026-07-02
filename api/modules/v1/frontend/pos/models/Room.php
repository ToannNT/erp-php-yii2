<?php

namespace api\modules\v1\frontend\pos\models;

use common\models\Order;
use common\models\Room as BaseRoom;

class Room extends BaseRoom
{
    public function fields()
    {
        return [
            "id",
            "name",
            "code",
            "group_id",
            "has_order" => "hasOrder",
        ];
    }

    public function getHasOrder()
    {
        return empty($this->firstOrder) === false;
    }

    public function getFirstOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ["external_id" => "id"])->onCondition([
            "status" => Order::STATUS_ORDER
        ]);
    }
}