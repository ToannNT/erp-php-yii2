<?php

namespace api\modules\v1\frontend\pos\models\search;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\frontend\pos\models\Order;

class HistoryOrderSearch extends Order
{
    public $q;
    public $start_date;
    public $end_date;

    public function fields(): array
    {
        return parent::fields();
    }

    public function rules(): array
    {
        return [
            [["status", "id", "type"], "integer"],
            ["q", "string"],
            [["start_date", "end_date"], "safe"],
            ["end_date", "addEndDate"],
            ["start_date", "addStartDate"],
        ];
    }

    public function addEndDate()
    {
        $this->end_date = date("Y-m-d 23:59:59", strtotime($this->end_date));
    }

    public function addStartDate()
    {
        $this->start_date = date("Y-m-d 00:00:00", strtotime($this->start_date));
    }

    public function setStartDateDefault()
    {
        if (!$this->start_date && !$this->end_date) {
            $this->start_date = date("Y-m-d", strtotime("-3 days"));
        }
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = self::find();
        $query->joinWith("client")
            ->joinWith("office")
            ->joinWith("orderItems")
            ->joinWith("promotion")
            ->joinWith("orderShip");
        // get order of user assign offices
        if (Yii::$app->user->can(User::ROLE_SELLER)) {
            $query->andWhere(["order.office_id" => array_column(Yii::$app->user->identity->offices, "id")]);
        }
        $query->channelPos()
            ->notOrder();
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
        $this->load($params, "");
        if (!$this->validate()) {
            return $dataProvider;
        }
        $this->setStartDateDefault();
        $query->andFilterWhere([
            "or",
            ["like", "order.code", $this->q],
            ["like", "customer.name", $this->q],
            ["like", "customer.code", $this->q]
        ]);
        $query->andFilterWhere([
            "order.status" => $this->status,
            "order.id" => $this->id,
            "order.type" => $this->type
        ]);
        $query->andFilterWhere(['>=', 'order.created_at', $this->start_date]);
        $query->andFilterWhere(['<=', 'order.created_at', $this->end_date]);
        return $dataProvider;
    }
}
