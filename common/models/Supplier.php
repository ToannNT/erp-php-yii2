<?php

namespace common\models;

use Yii;
use \common\models\base\Supplier as BaseSupplier;
use yii\helpers\ArrayHelper;

/**
 * Class Supplier
 * @property Contact $contact
 * @property Group[] $allGroup
 * @property string $groupHtml
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Supplier extends BaseSupplier
{
    const SUPPLIER_STATUS_ACTIVE = 'active';
    const SUPPLIER_STATUS_INACTIVE = 'inactive';

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
            "name"  => Yii::t("api", "Name"),
            "phone" => Yii::t("api", "Phone"),
            "tax_code" => Yii::t("api", "Tax Code"),
            "phone" => Yii::t("api", "Phone"),
            "website" => Yii::t("api", "Website"),
            "type"  => Yii::t("api", "Type"),
            "fax" => Yii::t("api", "Fax"),
            "supplier_status" => Yii::t("api", "Supplier Status"),
            "address_1" => Yii::t("api", "Address 1"),
            "address_2" => Yii::t("api", "Address 2"),
            "description" => Yii::t("api", "Description"),
            "note" => Yii::t("api", "Note")
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    /**
     * @return array|Group[]
     */
    public function getAllGroup()
    {
        return Group::find()->where(['id' => json_decode($this->group_id)])->all();
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

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'SUPN' . $tmp;
    }

    public function softDelete()
    {
        $this->status = Supplier::STATUS_DELETE;
        return $this->save(false);
    }
}
