<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\Supplier;
use common\models\Contact;
use common\models\Group;
use common\validators\IsArrayValidator;

class SupplierForm extends Supplier
{

    public $groups;

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->setFormatCode();
            $this->save(false);
        }
    }

    public function setGroup()
    {
        $dataGroup = [];
        foreach ($this->groups as $name) {
            $data = ["name" => $name, "type" => Group::TYPE_SUPPLIER];
            $group = Group::find()->where($data)->one();
            if (!$group) {
                $group = new Group($data);
                $group->save();
            }
            $dataGroup[] = $group->id;
        }
        $this->group_id = json_encode($dataGroup);
    }

    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'address_1'], 'required'],
            [['address_1', 'name', 'description', 'address_2', 'note'], 'string'],
            ['email', 'email'],
            ['groups', IsArrayValidator::class],
            [["website", "fax", "tax_code"], "string"],
            [['name'], 'unique', 'filter' => [
                '!=', 'status', Supplier::STATUS_DELETE
            ]],
            [['status'], 'in', 'range' => [
                Supplier::STATUS_ACTIVE, Supplier::STATUS_INACTIVE
            ]],
            [['status'], 'default', 'value' => Supplier::STATUS_INACTIVE],
            ['groups', 'default', 'value' => []],
            ['contact_id', "exist", 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id'], 'filter' => [
                '=', 'status', Contact::STATUS_ACTIVE
            ]]
        ];
    }
}
