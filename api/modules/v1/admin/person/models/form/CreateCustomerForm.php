<?php

namespace api\modules\v1\admin\person\models\form;

use api\modules\v1\admin\person\models\Customer;

class CreateCustomerForm extends Customer
{
    public $contact_status = 1;

    //contact model
    public $owner_first_name;
    public $owner_last_name;
    public $owner_phone;
    public $owner_email;
    public $owner_postal_code;
    public $owner_time_zone;
    public $owner_address_1;
    public $owner_address_2;
    public $owner_country;
    public $owner_state;
    public $owner_city;

    /**
     * @var mixed
     */

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['owner_first_name', 'owner_phone', 'owner_email'], 'required'],
            [['owner_email'], 'email'],
            [[
                'owner_postal_code', 'owner_time_zone', 'owner_address_1', 'owner_address_2', 'owner_country', 'owner_state', 'owner_city'
            ], 'safe'],
        ]);
    }

    public function mapField($map)
    {
        $newMap = [];
        foreach ($map as $item => $key) {
            $newMap[$key] = $this->$item;
        }
        return $newMap;
    }

    public function attributeContacts()
    {
        return [
            "owner_first_name" => "first_name",
            "owner_first_name" => "name",
            "owner_last_name" => "last_name",
            "owner_phone" => "phone",
            "owner_email" => "email",
            "owner_postal_code" => "postal_code",
            "owner_address_1" => "address_1",
            "owner_address_2" => "address_2",
            "owner_country" => "country",
            "owner_state" => "state",
            "owner_city" => "city",
            "contact_status" => "status"
        ];
    }
}
