<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\Department;
use api\modules\v1\admin\setting\models\Office;
use common\models\User;

class DepartmentForm extends Department
{

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [['name', 'office_id'], 'required'],
            [['status'], 'default', 'value' => Department::STATUS_ACTIVE],
            [['name'], 'unique', 'filter' => [
                '!=', 'status', Department::STATUS_DELETE
            ]],
            [
                "office_id", "exist", "targetClass" => Office::class, 'targetAttribute' => ['office_id' => 'id'], 'filter' => [
                    '=', 'status', Office::STATUS_ACTIVE
                ],
            ],
            ["user_id", "exist", "targetClass" => User::class, 'targetAttribute' => ['user_id' => 'id'], 'filter' => [
                '=', 'status', User::STATUS_ACTIVE
            ]],
            ["status", "in", "range" => [
                Department::STATUS_ACTIVE,
                Department::STATUS_INACTIVE
            ]]
        ];
    }

    public function softDelete()
    {
        $this->status = Department::STATUS_DELETE;
        return $this->save(false);
    }
}
