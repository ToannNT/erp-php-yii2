<?php

namespace common\models;

use Yii;
use \common\models\base\Contact as BaseContact;
use common\traits\SoftDeleteTrait;
use yii\helpers\ArrayHelper;

/**
 * Class Contact
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Contact extends BaseContact
{
    use SoftDeleteTrait;
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public function attributeLabels()
    {
        return [
            "name"       => Yii::t("api", "Name"),
            "phone"      => Yii::t("api", "Phone"),
            "created_at" => Yii::t("api", "Created At"),
            "status"     => Yii::t("api", "Status"),
            "first_name" => Yii::t("api", "First Name"),
            "last_name"  => Yii::t("api", "Last Name"),
            "postal_code" => Yii::t("api", "Postal Code"),
            "time_zone"  => Yii::t("api", "Time Zone"),
            "address_1"  => Yii::t("api", "Address 1"),
            "address_2"  => Yii::t("api", "Address 2"),
            "country"    => Yii::t("api", "Country"),
            "city"       => Yii::t("api", "City"),
            "state"      => Yii::t("api", "State")
        ];
    }
}
