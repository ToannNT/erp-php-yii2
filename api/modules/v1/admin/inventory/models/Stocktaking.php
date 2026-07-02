<?php

namespace api\modules\v1\admin\inventory\models;

use common\behaviors\JsonBehavior;
use yii\helpers\ArrayHelper;
use common\models\Stocktaking as BaseStocktaking;

class Stocktaking extends BaseStocktaking
{

    public function fields()
    {
        return [
            "id",
            "code",
            "office",
            "office_id",
            "inventory",
            "inventory_id",
            "note",
            "tags",
            "total_difference",
            "total_adjustment",
            "created_by" => function () {
                if ($this->createdBy) {
                    return $this->createdBy->username;
                }
            },
            "stocktaking_date",
            "progress_status",
            "status",
            "created_at",
            "updated_at"
        ];
    }

    public function extraFields()
    {
        return [
            "stocktaking_items" => "stocktakingItems",
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["tags", "progress_status"]
        ];
        return $behaviors;
    }

    public function addProgressStatus($status)
    {
        $progress_status = json_decode(json_encode($this->progress_status), true);
        $progress_status = ArrayHelper::merge($progress_status, [
            [
                "status" => $status,
                "date" => date("Y-m-d H:i:s")
            ]
        ]);
        $this->progress_status = $progress_status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }

    public function getInventory()
    {
        return parent::getInventory()->addSelect(["id", "name"]);
    }

    public function getStocktakingItems()
    {
        return $this->hasMany(StocktakingItem::class, ["stocktaking_id" => "id"]);
    }

    public function formName()
    {
        return "";
    }
}
