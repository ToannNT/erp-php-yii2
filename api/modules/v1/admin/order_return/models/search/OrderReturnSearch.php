<?php

namespace api\modules\v1\admin\order_return\models\search;

use common\models\Office;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\order_return\models\OrderReturn;

/**
 * OrderReturnSearch represents the model behind the search form of `api\modules\v1\admin\order_return\models\OrderReturn`.
 */
class OrderReturnSearch extends OrderReturn
{
    public $q;
    public $client_name;
    public $office_name;
    public $inventory_name;
    public $phone;
    public $start_date;
    public $end_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'discount_type', 'status', 'quantity', 'created_by', 'note', 'office_id'], 'integer'],
            [['code', 'data_delivery_fee', 'other_fee', 'progress_status', 'created_at', 'updated_at', 'deleted_at', 'client_name', 'office_name', 'inventory_name', 'phone', 'q'], 'safe'],
            [['discount_value', 'discount', 'delivery_fee', 'unit_price', 'total_price', 'sub_total_price'], 'number'],
            ["start_date", "addStartDate"],
            ["end_date", "addEndDate"]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = OrderReturn::find()
            ->joinWith("createdBy")
            ->joinWith("client")
            ->joinWith("orderReturnItems")
            ->joinWith("inventory")
            ->joinWith("order");
        // add conditions that should always apply here
        if (Yii::$app->user->can(User::ROLE_SELLER) || Yii::$app->user->can(User::ROLE_MANAGER)) {
            $query->andWhere(["order.office_id" => array_column(Yii::$app->user->identity->offices, "id")]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
            "attributes" => array_merge($this->attributes(), [
                "created_by" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'user_username'
                ],
                "client_name" => [
                    'asc' => ['client.name' => SORT_ASC],
                    'desc' => ['client.name' => SORT_DESC],
                    'label' => 'client_name'
                ],
            ])
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'order_return.id' => $this->id,
            'order_return.order_id' => $this->order_id,
            'order_return.discount_value' => $this->discount_value,
            'order_return.discount_type' => $this->discount_type,
            'order_return.discount' => $this->discount,
            'order_return.delivery_fee' => $this->delivery_fee,
            'order_return.status' => $this->status,
            'order_return.quantity' => $this->quantity,
            'order_return.unit_price' => $this->unit_price,
            'order_return.total_price' => $this->total_price,
            'order_return.sub_total_price' => $this->sub_total_price,
            'order_return.created_by' => $this->created_by,
            'order_return.note' => $this->note,
            'order_return.office_id' => $this->office_id,
            'order_return.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere([
            'or',
            ['like', 'order_return.code', $this->q],
            ['like', 'customer.phone', $this->q],
            ['like', 'customer.name', $this->q]
        ]);
        $query->andFilterWhere(['>=', '{{order_return}}.created_at', $this->start_date])
            ->andFilterWhere(['<=', '{{order_return}}.created_at', $this->end_date]);

        $query->andFilterWhere(['like', 'order_return.code', $this->code])
            ->andFilterWhere(['like', 'order_return.data_delivery_fee', $this->data_delivery_fee])
            ->andFilterWhere(['like', 'order_return.other_fee', $this->other_fee])
            ->andFilterWhere(['like', 'order_return.progress_status', $this->progress_status])
            ->andFilterWhere(['like', 'order_return.created_at', $this->created_at])
            ->andFilterWhere(['like', 'order_return.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'customer.name', $this->client_name])
            ->andFilterWhere(['like', 'customer.phone', $this->phone])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name])
            ->andFilterWhere(['like', 'office.name', $this->office_name]);

        return $dataProvider;
    }
}
