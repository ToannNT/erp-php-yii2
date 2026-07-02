<?php

namespace common\models;

use Yii;
use \common\models\base\SystemRbacMigration as BaseSystemRbacMigration;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "system_rbac_migration".
 */
class SystemRbacMigration extends BaseSystemRbacMigration
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
