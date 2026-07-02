<?php

namespace common\models;

use Yii;
use \common\models\base\RbacAuthItem as BaseRbacAuthItem;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "rbac_auth_item".
 */
class RbacAuthItem extends BaseRbacAuthItem
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
