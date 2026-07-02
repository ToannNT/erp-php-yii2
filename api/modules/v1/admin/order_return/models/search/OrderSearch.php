<?php

namespace api\modules\v1\admin\order_return\models\search;

use api\modules\v1\admin\order_return\models\Order;
use common\models\Order as OrderAlias;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * OrderSearch represents the model behind the search form about `api\modules\v1\admin\order\models\Order`.
 */
class OrderSearch extends Order
{
    public $q;
    public $start_date;
    public $end_date;
    public $client_name;
    public $office_name;
    public $inventory_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'created_at', 'updated_at', 'code', 'channel', 'q', 'client_name', 'inventory_name', 'office_name'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()
            ->joinWith("office")
            ->joinWith("inventory")
            ->joinWith("client")
            ->joinWith("createdBy")
            ->joinWith(["orderItems"])
            ->andWhere(["order.status" => OrderAlias::STATUS_DONE])
            ->andhaving([">", new Expression("sum(`order_item`.`quantity`)"), new Expression("sum(`order_item`.`quantity_return`)")])
            ->groupBy("order.id");
        if (!Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
            $offices = Yii::$app->user->identity->offices;
            $query->andWhere(['in', 'order.office_id', array_column($offices, 'id')]);
            if (Yii::$app->user->can(User::ROLE_SELLER)) {
                $query->channelPos();
            };
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
            "attributes" => array_merge($this->attributes(), [
                "office_name" => [
                    'asc' => ['office.name' => SORT_ASC],
                    'desc' => ['office.name' => SORT_DESC],
                    'label' => 'office_name'
                ],
                "inventory_name" => [
                    'asc' => ['inventory.name' => SORT_ASC],
                    'desc' => ['inventory.name' => SORT_DESC],
                    'label' => 'inventory_name'
                ],
                "client_name" => [
                    'asc' => ['client.name' => SORT_ASC],
                    'desc' => ['client.name' => SORT_DESC],
                    'label' => 'client_name'
                ]
            ])
        ]);

        $query->andFilterWhere([
            'order.id' => $this->id,
            'order.office_id' => $this->office_id,
            'order.inventory_id' => $this->inventory_id,
            'order.client_id' => $this->client_id,
            'order.total_price' => $this->total_price,
            'order.tax_price' => $this->tax_price,
            'order.discount' => $this->discount,
            'order.delivery_fee' => $this->delivery_fee,
            'order.deleted_at' => $this->deleted_at,
            'order.status' => $this->status,
            'order.quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['>=', '{{order}}.created_at', $this->start_date])
            ->andFilterWhere(['<=', '{{order}}.created_at', $this->end_date]);

        $query->andFilterWhere([
            'or',
            ['like', 'order.code', $this->q],
            ['like', 'customer.phone', $this->q],
            ['like', 'customer.name', $this->q]
        ]);

        $query->andFilterWhere(['like', 'order.code', $this->code])
            ->andFilterWhere(['like', 'order.channel', $this->channel])
            ->andFilterWhere(['like', 'customer.name', $this->client_name])
            ->andFilterWhere(['like', 'office.name', $this->office_name])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name]);
        return $dataProvider;
    }
}