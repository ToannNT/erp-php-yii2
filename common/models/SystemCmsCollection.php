<?php

namespace common\models;

use common\behaviors\JsonBehavior;
use \common\models\base\SystemCmsCollection as BaseSystemCmsCollection;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "system_cms_collection".
 */
class SystemCmsCollection extends BaseSystemCmsCollection
{
    const TYPE_BASE = 1;
    const TYPE_AUTH = 2;
    const TYPE_TEXT = "text";
    const TYPE_SLUG = "slug";
    const TYPE_EDITOR = "editor";
    const TYPE_NUMBER = "number";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_EMAIL = "email";
    const TYPE_URL = "url";
    const TYPE_FILE = "file";
    const TYPE_RELATION = "relation";
    const TYPE_SELECT = "select";

    const TYPE_RELATION_MULTIPLE = "multiple";
    const TYPE_JSON = "json";
    const TYPE_DATE_TIME = "date_time";

    const SCHEMA_TYPES = [
        "text",
        "slug",
        "editor",
        "number",
        "boolean",
        "email",
        "url",
        "file",
        "select",
        "relation",
        "json",
        "date_time"
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors["json"] = [
            "class" => JsonBehavior::class,
            'jsonAttributes' => ["schemas", "indexs"],
            'eventMaps' => [
                Model::EVENT_BEFORE_VALIDATE => "encode",
                Model::EVENT_AFTER_VALIDATE => "decode",
            ]
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

    public function formName()
    {
        return "";
    }
}
