<?php

namespace common\models;

use Yii;
use \common\models\base\DeliveryMethod as BaseDeliveryMethod;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "delivery_method".
 */
class DeliveryMethod extends BaseDeliveryMethod
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

    /**
     * Xoá mềm: giữ bản ghi để đơn cũ vẫn tra được tên phương thức giao hàng đã dùng.
     */
    public function softDelete(): bool
    {
        $this->status = self::STATUS_DELETE;
        $this->is_default = 0;
        return $this->save(false);
    }

    /**
     * Chỉ 1 phương thức được là mặc định — bỏ cờ is_default ở tất cả phương thức còn lại.
     */
    public function markAsOnlyDefault(): void
    {
        static::updateAll(["is_default" => 0], ["<>", "id", $this->id]);
    }

    /**
     * Phương thức mặc định dùng khi client không chọn: ưu tiên is_default, fallback active đầu tiên.
     */
    public static function findDefault(): ?DeliveryMethod
    {
        return static::find()->active()->isDefault()->one()
            ?: static::find()->active()->orderBy(["id" => SORT_ASC])->one();
    }
}
