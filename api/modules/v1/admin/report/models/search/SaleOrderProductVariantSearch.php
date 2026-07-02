<?php

namespace api\modules\v1\admin\report\models\search;

use api\modules\v1\admin\report\models\SaleOrderSearchProductVariant;
use common\models\Order;
use common\models\OrderItem;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;

class SaleOrderProductVariantSearch extends SaleOrderSearchProductVariant
{
    public $supplier_name;
    public $sku;
    public $supplier_id;
    public $start_date;
    public $end_date;

    public function rules(): array
    {
        return [
            [["start_date", "end_date"], "safe"],
            [["bought", "supplier_id"], "integer"],
            [["product_variant_name", "product_variant_sku", "supplier_name", "sku"], "string"],
            ["end_date", "addEndDate"],
            ["start_date", "addStartDate"]
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

    public function search($params)
    {
        $this->load($params);

        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $query
            ->joinWith("suppliers")
            ->joinWith("productVariant")
            ->groupBy("order_item.product_variant_id")
            ->joinWith("order", true, "JOIN")
            ->andWhere(["order.status" => Order::STATUS_DONE])
            ->addSelect("SUM(`order_item`.`sub_total`) as `sum_sub_total`, SUM(`order_item`.`total_price`) as `payment_before_return`,SUM(`order_item`.`discount_price`) as `total_discount_price`, order_item.*, SUM(`order_item`.`quantity`) as `bought`, SUM(`order_item`.`quantity_return`) as `total_quantity_return`");
        if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $query->andWhere(["supplier_id" => array_column(Yii::$app->user->identity->suppliers, "id")]);
        } elseif (Yii::$app->user->can(User::ROLE_MANAGER)) {
            $query->andWhere(["order.office_id" => array_column(Yii::$app->user->identity->offices, "id")]);
        }
        if (!$this->validate()) {
            return $dataProvider;
        }
        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "product_variant_sku" => [
                    'asc' => ['product_variant.sku' => SORT_ASC],
                    'desc' => ['product_variant.sku' => SORT_DESC],
                    'label' => 'product_variant_sku'
                ],
                "product_variant_name" => [
                    'asc' => ['product_variant.name' => SORT_ASC],
                    'desc' => ['product_variant.name' => SORT_DESC],
                    'label' => 'product_variant_name'
                ],
                "bought" => [
                    'asc' => ['bought' => SORT_ASC],
                    'desc' => ['bought' => SORT_DESC],
                    'label' => 'bought'
                ],
            ])
        ]);
        $query->andFilterWhere(['>=', '{{order_item}}.created_at', $this->start_date])
            ->andFilterWhere(['<=', '{{order_item}}.created_at', $this->end_date])
            ->andFilterWhere(['LIKE', '{{product_variant}}.name', $this->product_variant_name])
            ->andFilterWhere(['LIKE', '{{product_variant}}.sku', $this->product_variant_sku])
            ->andFilterWhere(['LIKE', '{{supplier}}.name', $this->supplier_name])
            ->andFilterWhere(["supplier_id" => $this->supplier_id])
            ->andFilterWhere(["LIKE", "product_variant.sku", $this->sku]);
        return $dataProvider;
    }
}