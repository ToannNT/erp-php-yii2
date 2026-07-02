<?php

namespace api\modules\v1\admin\setting\models;

use common\models\Office as BaseOffice;

class Office extends BaseOffice
{
    public function fields()
    {
        return [
            "id",
            "type",
            "name",
            "description",
            "address1",
            "address2",
            "contact_person" => "contactPerson",
            "province_code",
            "district_code",
            "ward_code",
            "created_at",
            "updated_at",
            "deleted_at",
            "status",
            "email",
            "latitude",
            "longitude"
        ];
    }

    public function formName()
    {
        return "";
    }

    public function getContactPerson()
    {
        return parent::getContactPerson()->addSelect(["id", "name"]);
    }
}
