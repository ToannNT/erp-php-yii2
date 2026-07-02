<?php

namespace common\models;

use Yii;
use \common\models\base\Customer as BaseCustomer;
use yii\helpers\ArrayHelper;

/**
 * Class Customer
 * @property Contact $owner
 * @property Group[] $allGroup
 * @property string $groupHtml
 * @property int $countContact
 * @property int $countNote
 * @property int $countOrder
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Customer extends BaseCustomer
{

    const STATUS_DELETE = -99;
    const TYPE_NORMAL = 'normal';
    const TYPE_VIP = 'vip';
    const TYPE_NEW = 'new';
    const TYPE_ATRISK = 'atrick';

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
            "name" => Yii::t("api", "Name"),
            "phone" => Yii::t("api", "Phone"),
            "address_1" => Yii::t("api", "Address 1"),
            "address_2" => Yii::t("api", "Address 2"),
            "website" => Yii::t("api", "Website"),
            "type" => Yii::t("api", "Type"),
            "country" => Yii::t("api", "Country"),
            "city" => Yii::t("api", "City"),
            "state" => Yii::t("api", "State"),
            "note" => Yii::t("api", "Note"),
            "status" => Yii::t("api", "Status"),
            "postal_code" => Yii::t("api", "Postal Code")
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Contact::class, ['id' => 'owner_id']);
    }

    /**
     * @return array|Group[]
     */
    public function getAllGroup()
    {
        return Group::find()->where(['id' => json_decode($this->groups)])->all();
    }

    public function getGroupHtml()
    {
        $groups = $this->allGroup;
        $string = '';
        foreach ($groups as $group) {
            $str = '<span class="btn btn-sm btn-outline-light mr-1">' . $group->name . '</span>';
            $string = $string . $str;
        }
        return $string;
    }

    /**
     * @return int
     */
    public function getCountContact()
    {
        return (int)$this->hasMany(ContactCustomer::class, ['customer_id' => 'id'])
            ->andWhere(["contact_customer.status" => null])
            ->count();
    }

    /**
     * @return int
     */
    public function getCountNote()
    {
        return (int)$this->hasMany(CustomerNote::class, ['customer_id' => 'id'])
            ->count();
    }

    /**
     * @return int
     */
    public function getCountOrder()
    {
        return (int)$this->hasMany(Order::class, ['client_id' => 'id'])->count();
    }

    public function setFormatCode()
    {
        $tmp = sprintf(" % '.07d", $this->id);
        $this->code = 'CUSN' . $tmp;
    }
}
