<?php

namespace api\modules\v1\admin\general\models;

use common\models\Inventory as BaseInventory;

class Inventory extends BaseInventory
{
    public function fields()
    {
        return [
            "id",
            "name",
            "office_id"
        ];
    }

    public function formName()
    {
        return "";
    }
}
