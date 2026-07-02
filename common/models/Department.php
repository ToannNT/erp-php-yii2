<?php

namespace common\models;

use Yii;
use \common\models\base\Department as BaseDepartment;
use yii\helpers\ArrayHelper;

/**
 * Class Department
 * @property Office $office
 * @property User $departmentHead
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Department extends BaseDepartment
{
    const STATUS_DELETE = -99;
    const STATUS_ACTIVE=1;
    const STATUS_INACTIVE=0;

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
            "name"      => Yii::t("api", "Name"),
            "office_id" => Yii::t("api", "Office"),
            "user_id"   => Yii::t("api", "User")
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentHead()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
