<?php

namespace common\models;

use Yii;
use \common\models\base\OfficePolicy as BaseOfficePolicy;
use yii\helpers\ArrayHelper;

/**
 * Class OfficePolicy
 * @property Office $office
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class OfficePolicy extends BaseOfficePolicy
{

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
                [['name', 'office_id', 'description'], 'required']
            ]
        );
    }

    public function attributeLabels()
    {
        return [
            "name"          => Yii::t("api", "Name"),
            "description"   => Yii::t("api", "Description")
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }
}
