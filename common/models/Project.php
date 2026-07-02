<?php

namespace common\models;

use Yii;
use \common\models\base\Project as BaseProject;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "project".
 */
class Project extends BaseProject
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
