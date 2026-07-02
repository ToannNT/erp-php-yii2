<?php

namespace api\modules\v1\admin\setting\models;

use common\models\SubDepartment as BaseSubDepartment;

class SubDepartment extends BaseSubDepartment
{
    public function fields()
    {
        return [
            "id",
            "name",
            "user" => "subDepartmentHead",
            "department" => "department",
            "status",
            "created_at",
            "updated_at",
            "deleted_at"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getSubDepartmentHead()
    {
        return parent::getSubDepartmentHead()->addSelect([
            "id", "username"
        ]);
    }

    public function getDepartment()
    {
        return parent::getDepartment()->addSelect(["id", "name"]);
    }
}
