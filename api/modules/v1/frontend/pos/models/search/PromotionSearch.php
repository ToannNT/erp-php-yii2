<?php

namespace api\modules\v1\frontend\pos\models\search;

use common\models\Promotion;
use Yii;
use yii\data\ActiveDataProvider;

class PromotionSearch extends Promotion
{

    public function fields()
    {
        return [
            "id",
            "code",
            "title",
            "offices" => function () {
                return json_decode($this->offices);
            },
            "order_total_required"
        ];
    }

    public $q;

    public function rules()
    {
        return [
            ["q", "string"]
        ];
    }

    public function search($params)
    {
        $offices = array_column(Yii::$app->user->identity->offices, "id");
        $query = self::find()->active()->available();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
                'pageSize' => 100,
            ],
            'sort' => [
                'params' => $params,
            ],
        ]);
        $this->load($params, "");
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "or",
            ["like", "code", $this->q],
            ["like", "title", $this->q]
        ])
            ->andWhere("JSON_CONTAINS(JSON_EXTRACT(`offices`, '$[*].id'),'[" . implode(",", $offices) . "]','$')");
        return $dataProvider;
    }
}
