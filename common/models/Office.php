<?php

namespace common\models;

use Yii;
use \common\models\base\Office as BaseOffice;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Class Office
 * @property Country $countryCode
 * @property Contact $contactPerson
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Office extends BaseOffice
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;
    const TYPE_CORPORATION = "corporation";
    const TYPE_EXEMPT_ORGANIZATION = "exempt_organization";
    const TYPE_PARTNERSHIP = "partnership";
    const TYPE_PRIVATE_FOUNDATION = "private_foundation";
    const TYPE_LIMITED_LIABILITY = "limited_liability";

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'softDeleteBehavior' => [
                    'class' => SoftDeleteBehavior::className(),
                    'softDeleteAttributeValues' => [
                        'status' => Inventory::STATUS_DELETE,
                        'deleted_at' => date("Y-m-d H:i:s")
                    ],
                ]
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
            "address" => Yii::t("api", "Address"),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountryCode()
    {
        return $this->hasOne(Country::class, ['iso2' => 'country']);
    }

    public function getCountry()
    {
        return $this->hasOne(Province::class, ["id" => "country"]);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::class, ["id" => "country"]);
    }

    public function getCity()
    {
        return $this->hasOne(District::class, ["id" => "city"]);
    }

    public function getState()
    {
        return $this->hasOne(Ward::class, ["id" => "state"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactPerson()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_person_id']);
    }

    /**
     * Here is method get office of user assign if permistion with manager
     * @author khuongdev 2001
     */
    static function getOfficeAssignUser(): array
    {
        $offices = self::find()->active();
        if (Yii::$app->user->can("manager")) {
            /* Get Offices assign manager */
            $userLogged = Yii::$app->user->identity;
            $offices->where(["in", "id", array_column($userLogged->offices, 'id')]);
        }
        return $offices->all();
    }

    /**
     * @return OfficeQuery
     * @author khuongdev2001
     * Here get office assign Role and Office is active
     *
     */
    static function withRole(): OfficeQuery
    {
        $query = self::find()->active();
        if (Yii::$app->user->can(User::ROLE_MANAGER)) {
            $query->andWhere(["id" => array_column(Yii::$app->user->identity->offices, "id")]);
        }
        return $query;
    }

    public function getInventorys()
    {
        return $this->hasMany(Inventory::class, ["office_id" => "id"])
            ->andWhere(["status" => Inventory::STATUS_ACTIVE]);
    }

    public function getInventories()
    {
        return $this->hasMany(Inventory::class, ["office_id" => "id"])
            ->andWhere(["status" => Inventory::STATUS_ACTIVE]);
    }
}
