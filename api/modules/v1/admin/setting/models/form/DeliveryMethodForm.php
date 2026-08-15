<?php

namespace api\modules\v1\admin\setting\models\form;

use api\modules\v1\admin\setting\models\DeliveryMethod;
use Yii;

class DeliveryMethodForm extends DeliveryMethod
{
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->getId();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Chỉ 1 phương thức được là mặc định — bật is_default ở đây thì tự tắt ở các phương thức còn lại.
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->is_default) {
            $this->markAsOnlyDefault();
        }
    }
}
