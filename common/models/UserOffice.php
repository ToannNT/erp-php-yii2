<?php

namespace common\models;

use Yii;
use \common\models\base\UserOffice as BaseUserOffice;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_office".
 */
class UserOffice extends BaseUserOffice
{

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

    public function getInventorys()
    {
        return $this->hasMany(Inventory::class, ["office_id" => "id"])
            ->viaTable("office", ["id" => "office_id"]);
    }
}
