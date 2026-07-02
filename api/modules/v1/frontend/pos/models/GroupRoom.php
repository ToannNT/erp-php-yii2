<?php

namespace api\modules\v1\frontend\pos\models;

use common\models\GroupRoom as BaseGroupRoom;
use yii\db\ActiveQuery;

class GroupRoom extends BaseGroupRoom
{
    public function fields()
    {
        return [
            "id",
            "name",
            "code"
        ];
    }

    public function extraFields()
    {
        return [
            "rooms" => "rooms"
        ];
    }

    public function getRooms(): ActiveQuery
    {
        return $this->hasMany(Room::class, ["group_id" => "id"])->with("firstOrder");
    }
}