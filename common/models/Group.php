<?php

namespace common\models;

use Yii;
use \common\models\base\Group as BaseGroup;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "group".
 */
class Group extends BaseGroup
{

    const TYPE_CUSTOMER = 'customer';
    const TYPE_SUPPLIER = 'supplier';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public static function createOrFind($name, $type = self::TYPE_CUSTOMER)
    {
        $group = self::find()->where(["name" => $name])->one();
        if (!$group) {
            $group = (new self(["name" => $name, "type" => $type]));
            $group->save(false);
        }
        return $group;
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

    public function formName()
    {
        return "";
    }
}
