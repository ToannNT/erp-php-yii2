<?php

namespace api\modules\v1\frontend\pos\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\frontend\pos\models\Order;

class OrderSearch extends Order
{

    public function rules()
    {
        return [
            [["status", "inventory_id", "type"], "integer"],
            ["external_id", "string"]
        ];
    }

    public function search($params)
    {
        $query = self::find();
        // get order of user assign offices
        if (Yii::$app->user->can("seller")) {
            $query->andWhere(["office_id" => array_column(Yii::$app->user->identity->offices, "id")]);
        }
        $query->channelPos()
            ->order()
            ->joinWith("client")
            ->joinWith("office")
            ->joinWith("orderItems")
            ->joinWith("promotion")
            ->joinWith("orderShip")
            ->joinWith("paymentMethods")
            ->groupBy("order.id");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
                'pageSizeLimit' => [1, 5000]
            ],
            'sort' => [
                'params' => $params,
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);
        if (!($this->load($params, "") && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "order.status" => $this->status,
            "order.type" => $this->type,
            "order.external_id" => $this->external_id,
        ]);
        return $dataProvider;
    }
}
