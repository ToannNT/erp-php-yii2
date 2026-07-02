<?php

namespace api\modules\v1\admin\inventory\models\form;

use api\modules\v1\admin\inventory\models\InventoryIssue;
use common\models\Inventory;
use common\validators\IsArrayValidator;
use Yii;

class InventoryIssueForm extends InventoryIssue
{

    public $issue_items;

    public function rules()
    {
        return [
            [["office_id", "inventory_id", "office_receive_id", "inventory_receive_id"], "required"],
            ["inventory_id", "compare", "compareAttribute" => "inventory_receive_id", "operator" => "!="],
            ["inventory_id", "exist", "targetClass" => Inventory::class, "targetAttribute" => ["inventory_id" => "id"], "filter" => [
                "=", "status", Inventory::STATUS_ACTIVE
            ]],
            ["inventory_receive_id", "exist", "targetClass" => Inventory::class, "targetAttribute" => ["inventory_receive_id" => "id"], "filter" => [
                "=", "status", Inventory::STATUS_ACTIVE
            ]],
            [["issue_items"], IsArrayValidator::class, "skipOnEmpty" => false],
            [["note"], "string"]
        ];
    }

    public function beforeSave($insert)
    {
        $this->type = InventoryIssue::TYPE_TRANSFER;
        $this->created_by = Yii::$app->user->getId();
        parent::beforeSave($insert);
        return true;
    }
}
