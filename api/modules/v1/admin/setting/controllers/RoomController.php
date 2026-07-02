<?php

namespace api\modules\v1\admin\setting\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\setting\models\form\GroupRoomForm;
use api\modules\v1\admin\setting\models\Room;
use api\modules\v1\admin\setting\models\search\RoomSearch;
use api\modules\v1\admin\setting\models\form\RoomForm;
use api\modules\v1\admin\setting\models\GroupRoom;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use api\helper\response\ApiConstant;
use api\modules\v1\admin\setting\models\search\GroupRoomSearch;

class RoomController extends Controller
{
    public function actionCreate(): array
    {
        $room = new RoomForm();
        $room->load(Yii::$app->request->post());
        if ($room->validate() && $room->save()) {
            return ResponseBuilder::responseJson(true, ["room" => $room], "Create Room successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $room->getErrors()], "Can't create room");
    }

    public function actionUpdate(): array
    {
        $request = Yii::$app->request;
        $id = $request->post("id");
        if (empty($id)) {
            return ResponseBuilder::responseJson(false, [], 'Empty param', ApiConstant::STATUS_BAD_REQUEST);
        }
        $room = RoomForm::find()->where(["id" => $id])->one();
        if (empty($room)) {
            return ResponseBuilder::responseJson(false, [], "Room not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $room->load($request->post());
        if ($room->validate() && $room->save()) {
            return ResponseBuilder::responseJson(true, ["room" => $room], "Update Room successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $room->getErrors()], "Can't update room");
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->post("id");
        if (empty($id)) {
            return ResponseBuilder::responseJson(false, [], 'Empty param', ApiConstant::STATUS_BAD_REQUEST);
        }
        $room = Room::find()->where(["id" => $id])->one();
        if (empty($room)) {
            return ResponseBuilder::responseJson(false, [], "Room not found", ApiConstant::STATUS_NOT_FOUND);
        }
        if ($room->delete()) {
            return ResponseBuilder::responseJson(true, ["group_room" => $room], "Delete room successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $room->getErrors()], "Can't delete room");
    }

    /**
     * @return array
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new RoomSearch())->search(Yii::$app->request->queryParams), "successfully");
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): array
    {
        $room = Room::find()->where(["id" => $id])->one();
        if (empty($room)) {
            throw new NotFoundHttpException("Room not found");
        }
        return ResponseBuilder::responseJson(true, ["room" => $room], "successfully");
    }


    /**
     * @return array
     */
    public function actionCreateGroup(): array
    {
        $groupRoom = new GroupRoomForm();
        $groupRoom->load(Yii::$app->request->post());
        if ($groupRoom->validate() && $groupRoom->save()) {
            return ResponseBuilder::responseJson(true, [], "Create Group successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $groupRoom->getErrors()], "Can't create Group Room");
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdateGroup(): array
    {
        $groupRoom = GroupRoomForm::find()->where(["id" => Yii::$app->request->post('id')])->one();
        if (empty($groupRoom)) {
            throw new NotFoundHttpException("GroupRoom not found");
        }
        $groupRoom->load(Yii::$app->request->post());
        if ($groupRoom->validate() && $groupRoom->save()) {
            return ResponseBuilder::responseJson(true, ["group_room" => $groupRoom], "Update Group successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $groupRoom->getErrors()], "Can't update Group Room");
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     * @throws NotFoundHttpException
     */
    public function actionDeleteGroup(): array
    {
        $request = Yii::$app->request;
        $id = $request->post("id");
        if (empty($id)) {
            return ResponseBuilder::responseJson(false, [], 'Empty param', ApiConstant::STATUS_BAD_REQUEST);
        }
        $groupRoom = GroupRoom::find()->where(["id" => $id])->one();
        if (empty($groupRoom)) {
            return ResponseBuilder::responseJson(false, [], "Group room not found", ApiConstant::STATUS_NOT_FOUND);
        }
        if ($groupRoom->delete()) {
            return ResponseBuilder::responseJson(true, ["group_room" => $groupRoom], "Delete Group successfully");
        }
        return ResponseBuilder::responseJson(false, ["errors" => $groupRoom->getErrors()], "Can't delete Group Room");
    }


    public function actionIndexGroup(): array
    {
        return ResponseBuilder::responseJson(true, (new GroupRoomSearch())->search(Yii::$app->request->queryParams), "successfully");
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionViewGroup($id): array
    {
        $groupRoom = GroupRoom::find()->where(["id" => $id])->one();
        if (empty($groupRoom)) {
            throw new NotFoundHttpException("GroupRoom not found");
        }
        return ResponseBuilder::responseJson(true, ["group_room" => $groupRoom], "successfully");
    }
}