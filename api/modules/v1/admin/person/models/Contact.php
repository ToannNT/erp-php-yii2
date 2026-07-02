<?php

namespace api\modules\v1\admin\person\models;

use common\models\Contact as BaseContact;

class Contact extends BaseContact
{
    public function fields()
    {
        return [
            "id",
            "first_name",
            "last_name",
            "name",
            "description",
            "note",
            "phone",
            "email",
            "status",
            "address_1",
            "address_2",
            "created_at",
            "updated_at",
        ];
    }

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['first_name', 'last_name', 'email', 'phone'], 'required'],
            [['phone', 'email'], 'unique', 'filter' => [
                '<>', 'status', BaseContact::STATUS_DELETE
            ]],
            [['email'], 'email'],
            [['status'], 'default', 'value' => BaseContact::STATUS_ACTIVE],
            [['name'], 'default', 'value' => function ($model) {
                return $this->first_name . ' ' . $this->last_name;
            }]
        ]);
    }
}
