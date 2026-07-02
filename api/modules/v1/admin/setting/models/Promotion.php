<?php

namespace api\modules\v1\admin\setting\models;

use common\components\log\BuildLogDbTarget;
use common\components\log\DbTarget;
use common\models\Promotion as BasePromotion;
use common\validators\IsArrayValidator;
use common\behaviors\JsonBehavior;
use Yii;

class Promotion extends BasePromotion
{

    public function fields()
    {
        return [
            "id",
            "title",
            "code",
            "offices",
            "description",
            "discount_type",
            "discount_value",
            "start_date",
            "end_date",
            "limit",
            "used",
            "order_total_required",
            "group_customers",
            "status" => function ($model) {
                if ($model->status == BasePromotion::STATUS_INACTIVE) {
                    return $model->status;
                } else if ($model->end_date && strtotime($model->end_date) < time()) {
                    return BasePromotion::STATUS_EXPIRED;
                } else if ($model->limit && $model->limit < $model->used) {
                    return BasePromotion::STATUS_EXPIRED;
                }
                return BasePromotion::STATUS_ACTIVE;
            },
            "condition_type",
            "condition_items",
            "apply_for_all",
            "created_at",
            "updated_at"
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $tag = DbTarget::TAG_UPDATED;
        $task = "Update Promotion";
        if ($insert) {
            $tag = DbTarget::TAG_CREATED;
            $task = "Create Promotion";
        }
        Yii::$app->build_log->push($task, __METHOD__, $tag, $this->getAttributes(), $changedAttributes);
    }

    public function formName()
    {
        return "";
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["condition_items", "offices"]
        ];
        return $behaviors;
    }

    public function rules()
    {
        return [
            [["title", "code", "discount_type", "discount_value", "start_date"], "required"],
            [["code"], "unique", "filter" => [
                "!=", "status", BasePromotion::STATUS_DELETE
            ]],
            [["description"], "string"],
            [["promotion_type", "limit", "order_total_required"], "integer"],
            [["start_date", "end_date"], "date", 'format' => 'php:Y-m-d H:i:s'],
            [["discount_value"], "number", "min" => 0],
            [["discount_type"], "discountTypeRule"],
            [["discount_type"], "in", 'range' => [BasePromotion::DISCOUNT_PERCENT, BasePromotion::DISCOUNT_PRICE, BasePromotion::DISCOUNT_SAME_PRICE]],
            [["status"], "default", "value" => BasePromotion::STATUS_INACTIVE],
            [["offices", "condition_items"], IsArrayValidator::class],
            ["condition_type", "in", "range" => [BasePromotion::PROMOTION_PRODUCT, BasePromotion::PROMOTION_ORDER, BasePromotion::PROMOTION_BRAND, BasePromotion::PROMOTION_CATEGORY, BasePromotion::PROMOTION_SUPPLIER]],
            [["used", "apply_for_all"], "default", "value" => 0],
            ["apply_for_all", "in", "range" => [BasePromotion::STATUS_INACTIVE, BasePromotion::STATUS_ACTIVE]]
        ];
    }

    public function discountTypeRule($attribute)
    {
        if ($this->discount_type == BasePromotion::DISCOUNT_PERCENT && $this->discount_value > 100) {
            $this->addError($attribute, "Invalid");
        }
    }
}
