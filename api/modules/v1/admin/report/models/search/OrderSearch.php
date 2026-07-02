<?php

namespace api\modules\v1\admin\report\models\search;

use api\modules\v1\admin\report\models\Order;
use common\models\Order as OrderAlias;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use Exception;

class OrderSearch extends Order
{
    public $start_date;
    public $end_date;
    protected $sumOrder;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["start_date", "end_date"], "safe"],
            ["start_date", "addStartDate"],
            ["end_date", "addEndDate"]
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()
//            ->addSelect("order.*,
//             (SELECT SUM(`order_return`.`total_price`) FROM `order_return` WHERE `order_return`.`order_id` = `order`.`id`) as `order`.`total_price_return`,
//             (`total_price` - `total_price_return`) as `total_price_after_return`
//             ")
            ->unDelete()
            ->andWhere(["order.status" => OrderAlias::STATUS_DONE])
            ->joinWith("paymentMethods")
            ->groupBy("order.id");
        if (!Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
            $offices = Yii::$app->user->identity->offices;
            $query->andWhere(['in', 'office_id', array_column($offices, 'id')]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);
        if (!($this->load($params, "") && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'order.id' => $this->id,
            'order.office_id' => $this->office_id,
            'order.inventory_id' => $this->inventory_id,
            'order.client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['>=', '{{order}}.done_at', $this->start_date])
            ->andFilterWhere(['<=', '{{order}}.done_at', $this->end_date]);

        $query->andFilterWhere(['like', 'order.code', $this->code]);
        return $dataProvider;
    }

    /**
     * @throws Exception
     */
    public function getSumOrder()
    {
        return $this->sumOrder;
    }
}