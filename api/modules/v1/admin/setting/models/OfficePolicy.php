<?php

namespace api\modules\v1\admin\setting\models;

use common\models\OfficePolicy as OfficePolicyBase;

class OfficePolicy extends OfficePolicyBase
{

    public function fields()
    {
        return [
            "id",
            "name",
            "office" => "office",
            "description",
            "created_at",
            "updated_at",
            "status"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }
}
