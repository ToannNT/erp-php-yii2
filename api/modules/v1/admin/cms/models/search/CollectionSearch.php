<?php

namespace api\modules\v1\admin\cms\models\search;

use api\modules\v1\admin\cms\models\Collection;
use common\models\SystemCmsCollection as SystemCmsCollectionAlias;
use yii\data\ActiveDataProvider;

class CollectionSearch extends Collection
{
    public function search($params): ActiveDataProvider
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        foreach (Collection::$schemas as $schema) {
            switch ($schema["type"]) {
                case SystemCmsCollectionAlias::TYPE_URL:
                case SystemCmsCollectionAlias::TYPE_EMAIL:
                case SystemCmsCollectionAlias::TYPE_TEXT:
                case SystemCmsCollectionAlias::TYPE_EDITOR:
                    $query->andFilterWhere(["LIKE", self::$tableName . "." . $schema["name"], $params[$schema["name"]] ?? null]);
                    break;
            }
        }
        return $dataProvider;
    }
}