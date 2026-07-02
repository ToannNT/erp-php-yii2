<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\Room;
use api\modules\v1\admin\setting\models\GroupRoom;
use Yii;
use yii\helpers\Inflector;

class RoomForm extends Room
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [["group_id"], "required"],
            [["code"], "unique"],
            ['group_id', 'exist', 'targetClass' => GroupRoom::class, 'targetAttribute' => ['group_id' => 'id']],
            ['name', 'unique', 'targetAttribute' => ['group_id', 'name']],
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->id;
            $this->generateCode();
        }
    }

    public function generateCode()
    {
        if (empty($this->code)) {
            $this->code = Inflector::slug($this->name) . "-" . $this->id;
            $this->updateAttributes(['code' => $this->code]);
        }
    }
}