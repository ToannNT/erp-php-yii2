<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\Inventory;

class InventoryForm extends Inventory
{
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->setFormatCode();
            $this->save(false);
        }
    }
}
