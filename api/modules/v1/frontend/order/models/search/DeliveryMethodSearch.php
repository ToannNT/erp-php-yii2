<?php

namespace api\modules\v1\frontend\order\models\search;

use api\modules\v1\frontend\order\models\DeliveryMethod;
use yii\data\ActiveDataProvider;

class DeliveryMethodSearch extends DeliveryMethod
{
    public function rules()
    {
        return [
            [["name", "code"], "safe"],
        ];
    }

    /**
     * Chỉ trả phương thức đang bật; mặc định đứng đầu để website chọn sẵn.
     */
    public function search($params): ActiveDataProvider
    {
        $query = DeliveryMethod::find()
            ->active()
            ->orderBy(["is_default" => SORT_DESC, "id" => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            "query" => $query,
            "pagination" => [
                "params" => $params,
            ],
            "sort" => false
        ]);

        $this->load($params, "");

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(["like", "name", $this->name])
            ->andFilterWhere(["code" => $this->code]);

        return $dataProvider;
    }
}
