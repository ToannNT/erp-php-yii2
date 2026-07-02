<?php

namespace common\models;

use Yii;
use \common\models\base\AdministrativeUnit as BaseAdministrativeUnit;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "administrative_unit".
 */
class AdministrativeUnit extends BaseAdministrativeUnit
{

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
}
