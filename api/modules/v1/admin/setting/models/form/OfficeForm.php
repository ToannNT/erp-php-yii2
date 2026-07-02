<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\Inventory;
use api\modules\v1\admin\setting\models\Office;
use common\models\Contact;
use common\models\Department;
use common\models\District;
use common\models\OfficePolicy;
use common\models\Province;
use common\models\Ward;
use Yii;

class OfficeForm extends Office
{

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'address1'], 'required'],
            [['name'], 'unique', 'filter' => [
                '!=', 'status', Office::STATUS_DELETE
            ]],
            ["status", "integer"],
            ["status", 'in', 'range' => [Office::STATUS_ACTIVE, Office::STATUS_INACTIVE]],
            ["status", "default", "value" => Office::STATUS_ACTIVE],
            ["email", "email"],
            ["type", "default", "value" => Office::TYPE_CORPORATION],
            ["type", "in", "range" => [
                Office::TYPE_CORPORATION,
                Office::TYPE_EXEMPT_ORGANIZATION,
                Office::TYPE_PARTNERSHIP,
                Office::TYPE_PRIVATE_FOUNDATION
            ]],
            ["contact_person_id", "exist", 'targetClass' => Contact::class, 'targetAttribute' => ['contact_person_id' => 'id'], 'filter' => [
                '=', 'status', Contact::STATUS_ACTIVE
            ]],
            ["province_code", "exist", "targetClass" => Province::class, "targetAttribute" => ["province_code" => "code"]],
            ["district_code", "exist", "targetClass" => District::class, "targetAttribute" => ["district_code" => "code"]],
            ["ward_code", "exist", "targetClass" => Ward::class, "targetAttribute" => ["ward_code" => "code"]]
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->status == Office::STATUS_DELETE) {
            Inventory::updateAll(["status" => Inventory::STATUS_DELETE], ["office_id" => $this->id]);
            OfficePolicy::updateAll(["status" => OfficePolicy::STATUS_DELETE], ["office_id" => $this->id]);
        }
    }
}
