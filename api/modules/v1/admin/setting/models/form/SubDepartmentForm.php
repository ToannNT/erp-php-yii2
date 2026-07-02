<?php

namespace api\modules\v1\admin\setting\models\form;

use common\models\Department;
use common\models\User;
use api\modules\v1\admin\setting\models\SubDepartment;

class SubDepartmentForm extends SubDepartment
{
    public function rules()
    {
        return [
            [['name', 'department_id'], 'required'],
            ["department_id", "exist", "targetClass" => Department::class, 'targetAttribute' => ['department_id' => 'id'], 'filter' => [
                '=', 'status', SubDepartment::STATUS_ACTIVE
            ]],
            [['name'], 'unique', 'filter' => [
                '!=', 'status', SubDepartment::STATUS_DELETE
            ]],
            [["status"], "default", "value" => SubDepartment::STATUS_ACTIVE],
            ["user_id", "exist", "targetClass" => User::class, 'targetAttribute' => ['user_id' => 'id'], 'filter' => [
                '=', 'status', User::STATUS_ACTIVE
            ]],
            ["status", 'in', 'range' => [SubDepartment::STATUS_ACTIVE, SubDepartment::STATUS_INACTIVE]],
        ];
    }
}
