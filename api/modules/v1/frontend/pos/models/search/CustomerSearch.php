<?php

namespace api\modules\v1\frontend\pos\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\frontend\pos\models\Customer;

class CustomerSearch extends Customer
{

    public $q;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ["q", "string"],
            ["id", "integer"]
        ]);
    }

    public function search($params)
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
                'pageSizeLimit' => [1, 5000]
            ],
            'sort' => [
                'params' => $params,
            ],
        ]);
        if (!($this->load($params, "") && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "id" => $this->id,
        ]);
        $query->andFilterWhere([
            "or",
            ["like", "name", $this->q],
            ["like", "code", $this->q],
            ["like", "phone", $this->q]
        ]);
        return $dataProvider;
    }
}
