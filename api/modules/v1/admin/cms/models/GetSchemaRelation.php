<?php

namespace api\modules\v1\admin\cms\models;

use common\base\cms\GetRelationCore;
use yii\base\Model;

class GetSchemaRelation extends Model
{
    const IS_CMS = 1;
    const NOT_IS_CMS = 0;

    public function getTables()
    {
        return array_merge($this->getTableCores(), $this->getTableCms());
    }

    public function getTableCores()
    {
        $relationCore = new GetRelationCore();
        $tables = $relationCore->getTables();
        return array_map(function ($table) {
            return [
                "table_name" => $table,
                "is_cms" => self::NOT_IS_CMS
            ];
        }, $tables);
    }

    public function getTableCms()
    {
        $tables = SystemCmsCollection::find()->select("name")->column();
        return array_map(function ($table) {
            return [
                "table_name" => $table,
                "is_cms" => self::IS_CMS
            ];
        }, $tables);
    }
}