<?php

namespace api\modules\v1\admin\setting\models;

use common\models\Department as DepartmentBase;

class Department extends DepartmentBase
{
    public function fields()
    {
        return [
            "id",
            "office" => "office",
            "name",
            "user" => "departmentHead",
            "created_at",
            "updated_at"
        ];
    }

    public function getOffice()
    {
        return parent::getOffice()->addSelect(["id", "name"]);
    }

    public function getDepartmentHead()
    {
        return parent::getDepartmentHead()->addSelect(["id", "username"]);
    }
}
