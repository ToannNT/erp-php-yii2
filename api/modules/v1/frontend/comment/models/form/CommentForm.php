<?php

namespace api\modules\v1\frontend\comment\models\form;

use api\modules\v1\frontend\comment\models\Comment;
use common\models\Product;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

class CommentForm extends Comment
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'value' => date("Y-m-d h:i:s"),
            ]
        ]);
    }

    public function rules(): array
    {
        return [
            [["content", "module_id"], "required"],
            [["rating"], "integer"],
            [["status"], "default", "value" => self::STATUS_ACTIVE],
            ["type", 'in', 'range' => $this->getTypes(), 'allowArray' => true],
            ["type", "default", "value" => self::TYPE_PRODUCT],
            ["module_id", function ($attribute) {
                $module = (new Query())
                    ->select(['id'])
                    ->from($this->type == self::TYPE_PRODUCT
                        ? self::TYPE_PRODUCT
                        : self::TYPE_ARTICLE
                    )
                    ->where(['status' => Product::STATUS_ACTIVE])
                    ->andWhere(["id" => $this->module_id])
                    ->one();
                if (!$module) {
                    $this->addError($attribute, Yii::t("api", "{attribute} invalid"));
                }
            }]
        ];
    }
}