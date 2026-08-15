<?php

namespace api\modules\v1\admin\setting\models\search;

use api\modules\v1\admin\setting\models\PaymentMethod;
use yii\data\ActiveDataProvider;

class PaymentMethodSearch extends PaymentMethod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["id", "status", "is_default"], "integer"],
            [["name", "code", "created_at", "updated_at"], "safe"],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = PaymentMethod::find()->unDelete();

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
            "status" => $this->status,
            "is_default" => $this->is_default,
        ]);

        $query->andFilterWhere(["like", "name", $this->name])
            ->andFilterWhere(["like", "code", $this->code]);

        return $dataProvider;
    }
}
