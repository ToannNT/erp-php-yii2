<?php

namespace api\modules\v1\admin\order\models\search;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order as OrderAlias;
use api\modules\v1\admin\order\models\Order;

/**
 * OrderSearch represents the model behind the search form about `api\modules\v1\admin\order\models\Order`.
 */
class OrderSearch extends Order
{
    public $start_date;
    public $end_date;
    public $office_name;
    public $inventory_name;
    public $client_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office_id', 'inventory_id', 'client_id', 'status', 'quantity', 'promotion_id'], 'integer'],
            [['office_name', 'inventory_name', 'client_name', 'price_policy', 'tax', 'created_by', 'shipping_address', 'order_address', 'note', 'return_note', 'tags', 'delivery', 'created_at', 'done_at', 'updated_at', 'deleted_at', 'data_tax', 'data_discount', 'data_delivery_fee', 'code', 'channel', 'data_payments', 'progress_status'], 'safe'],
            [['total_price', 'tax_price', 'discount', 'delivery_fee', 'payments'], 'number'],
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
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
            ->unDelete()
            ->joinWith("office")
            ->joinWith("inventory")
            ->joinWith("client")
            ->joinWith("createdBy")
            ->joinWith("orderItems")
            ->joinWith("paymentMethods")
            ->joinWith("orderShip")
            ->groupBy("order.id");
        if (!Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
            $offices = Yii::$app->user->identity->offices;
            $query->andWhere(['in', 'order.office_id', array_column($offices, 'id')]);
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
            'order.promotion_id' => $this->promotion_id,
        ]);
        $query->andFilterWhere(['>=', '{{order}}.created_at', $this->start_date])
            ->andFilterWhere(['<=', '{{order}}.created_at', $this->end_date]);

        $query->andFilterWhere(['like', 'order.price_policy', $this->price_policy])
            ->andFilterWhere(['like', 'order.tax', $this->tax])
            ->andFilterWhere(['like', 'order.note', $this->note])
            ->andFilterWhere(['like', 'order.return_note', $this->return_note])
            ->andFilterWhere(['like', 'order.delivery', $this->delivery])
            ->andFilterWhere(['like', 'order.code', $this->code])
            ->andFilterWhere(['like', 'order.channel', $this->channel])
            ->andFilterWhere(['like', 'office.name', $this->office_name])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name])
            ->andFilterWhere(['like', 'customer.name', $this->client_name])
            ->andFilterWhere(['like', 'order.created_at', $this->created_at])
            ->andFilterWhere(['like', 'order.done_at', $this->done_at])
            ->andFilterWhere(['like', 'order.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'user.username', $this->createdBy])
            ->andFilterWhere(['like', 'order.payments', $this->payments]);

        return $dataProvider;
    }
}
