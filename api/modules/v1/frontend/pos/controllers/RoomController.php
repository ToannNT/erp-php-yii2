<?php

namespace api\modules\v1\frontend\pos\controllers;

use Yii;
use yii\rest\Controller;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\pos\models\GroupRoom;
use api\modules\v1\frontend\pos\models\Room;

class RoomController extends Controller
{
    /**
     * @return array
     */
    public function actionListAllGroup(): array
    {
        $groupRooms = GroupRoom::find()->with("rooms")->all();
        return ResponseBuilder::responseJson(true, ["group_rooms" => $groupRooms], "Successfully");
    }

    /**
     * @return array
     */
    public function actionListAllRoom(): array
    {
        $rooms = Room::find()->filterWhere([
            "group_id" => Yii::$app->request->get("group_id"),
        ])->all();
        return ResponseBuilder::responseJson(true, ["rooms" => $rooms], "Successfully");
    }
}