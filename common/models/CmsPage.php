<?php

namespace common\models;

use Yii;
use \common\models\base\CmsPage as BaseCmsPage;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cms_pages".
 */
class CmsPage extends BaseCmsPage
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
