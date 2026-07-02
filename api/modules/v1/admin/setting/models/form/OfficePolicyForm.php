<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\Office;
use api\modules\v1\admin\setting\models\OfficePolicy;

class OfficePolicyForm extends OfficePolicy
{
    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [["name", "office_id"], "required"],
            ["name", "unique", "filter" => [
                "!=", "status", OfficePolicy::STATUS_DELETE
            ]],
            ["description", "string"],
            ["office_id", "exist", "targetClass" => Office::class, 'targetAttribute' => ['office_id' => 'id'], 'filter' => [
                '=', 'status', OfficePolicy::STATUS_ACTIVE
            ]],
            ["status", "default", "value" => OfficePolicy::STATUS_INACTIVE]
        ];
    }

    public function softDelete()
    {
        $this->status = OfficePolicy::STATUS_DELETE;
        return $this->save(false);
    }
}
