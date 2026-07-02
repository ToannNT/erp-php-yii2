<?php

namespace common\models;

use Yii;
use \common\models\base\RbacAuthItemChild as BaseRbacAuthItemChild;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "rbac_auth_item_child".
 */
class RbacAuthItemChild extends BaseRbacAuthItemChild
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
