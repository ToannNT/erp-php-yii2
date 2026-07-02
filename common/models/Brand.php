<?php

namespace common\models;

use common\behaviors\JsonBehavior;
use Yii;
use \common\models\base\Brand as BaseBrand;
use common\traits\SoftDeleteTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "brand".
 */
class Brand extends BaseBrand
{
    use SoftDeleteTrait;

    const TYPE_ITEM = 'item';
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -99;

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'Brand' . $tmp;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["icon"]
        ];
        return $behaviors;
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t("api", 'ID'),
            'name' => Yii::t("api", 'Name'),
            'type' => Yii::t("api", 'Type'),
            'code' => Yii::t("api", 'Code'),
            'description' => Yii::t("api", 'Description'),
            'icon' => Yii::t("api", 'Icon'),
            'images' => Yii::t("api", 'Images'),
            'color' => Yii::t("api", 'Color'),
            'priority' => Yii::t("api", 'Priority'),
            'parent_id' => Yii::t("api", 'Parent ID'),
            'owner_id' => Yii::t("api", 'Owner ID'),
            'group_id' => Yii::t("api", 'Group ID'),
            'slug' => Yii::t("api", "Slug"),
            'created_at' => Yii::t("api", 'Created At'),
            'updated_at' => Yii::t("api", 'Updated At'),
            'deleted_at' => Yii::t("api", 'Deleted At'),
            'status' => Yii::t("api", 'Status'),
        ];
    }

    public function getCategoryBrands()
    {
        return $this->hasMany(CategoryBrand::class, ["brand_id" => "id"]);
    }
}
