<?php

namespace api\modules\v1\admin\setting\models\search;

use api\modules\v1\admin\setting\models\DeliveryMethod;
use yii\data\ActiveDataProvider;

class DeliveryMethodSearch extends DeliveryMethod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["id", "status", "is_default"], "integer"],
            [["fee"], "number"],
            [["name", "code", "created_at", "updated_at"], "safe"],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = DeliveryMethod::find()->unDelete();

        $dataProvider = new ActiveDataProvider([
            "query" => $query,
            "pagination" => [
                "params" => $params,
            ]
        ]);
        $dataProvider->setSort([
            "attributes" => $this->attributes(),
            "defaultOrder" => [
                "is_default" => SORT_DESC,
                "id" => SORT_ASC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            "id" => $this->id,
            "fee" => $this->fee,
            "status" => $this->status,
            "is_default" => $this->is_default,
        ]);

        $query->andFilterWhere(["like", "name", $this->name])
            ->andFilterWhere(["like", "code", $this->code]);

        return $dataProvider;
    }
}
