<?php

namespace api\modules\v1\admin\setting\models\form;

use common\models\GroupRoom;
use Yii;
use yii\helpers\Inflector;

class GroupRoomForm extends GroupRoom
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [["name"], "required"],
            ["name", "unique"]
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