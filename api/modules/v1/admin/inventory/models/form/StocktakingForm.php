<?php

namespace api\modules\v1\admin\inventory\models\form;

use api\modules\v1\admin\inventory\models\Stocktaking;
use common\validators\IsArrayValidator;
use common\models\Inventory;
use Yii;

class StocktakingForm extends Stocktaking
{
    public $stocktaking_items;

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->getId();
        }
        parent::beforeSave($insert);
        return true;
    }

    public function rules()
    {
        return [
            [["office_id", "inventory_id"], "required"],
            [["note"], "string"],
            [["tags"], "each", "rule" => ["string"]],
            ["stocktaking_items", IsArrayValidator::class, "skipOnEmpty" => false],
            [["inventory_id"], "inventoryValidator"],
            ["status", "default", "value" => Stocktaking::STATUS_PENDING]
        ];
    }

    public function inventoryValidator($attribute)
    {
        $inventory = Inventory::find()->where([
            "id" => $this->inventory_id
        ])
            ->andWhere([
                "office_id" => $this->office_id
            ])
            ->active()
            ->one();
        if (!$inventory) {
            $this->addError($attribute, "Inventory Or Office not found");
        }
    }
}
