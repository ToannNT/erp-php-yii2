<?php

namespace api\modules\v1\admin\inventory\models\search;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\inventory\models\InventoryReceipt;

class InventoryReceiptSearch extends InventoryReceipt
{

    public $office_name;
    public $inventory_name;
    public $supplier_name;
    public $username;

    public function rules()
    {
        return [
            [["id", "status", "total_discount_type"], "integer"],
            [["total_price"], "number"],
            [["code", "office_name", "inventory_name", "supplier_name", "username", "note", "created_at", "updated_at"], "string"]
        ];
    }

    public function search($params)
    {
        $query = self::find()
            ->joinWith("createdBy")
            ->joinWith("inventory")
            ->joinWith("office")
            ->joinWith("supplier");

        if (Yii::$app->user->can(User::ROLE_MANAGER)) {
            $offices = Yii::$app->user->identity->offices;
            $query->andWhere(['in', 'inventory_receipt.office_id', array_column($offices, 'id')]);
        } else if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $query->andWhere(["supplier.id" => array_column(Yii::$app->user->identity->suppliers, "id")]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->setSort([
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
                "supplier_name" => [
                    'asc' => ['supplier.name' => SORT_ASC],
                    'desc' => ['supplier.name' => SORT_DESC],
                    'label' => 'supplier_name'
                ],
                "username" => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                    'label' => 'username'
                ]
            ]),
            "defaultOrder" => [
                "id" => SORT_DESC
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'inventory_receipt.id' => $this->id,
            'inventory_receipt.deleted_at' => $this->deleted_at,
            'inventory_receipt.status' => $this->status,
            'inventory_receipt.total_discount_type' => $this->total_discount_type,
            'inventory_receipt.total_price' => $this->total_price
        ]);
        $query->andFilterWhere(['like', 'inventory_receipt.code', $this->code])
            ->andFilterWhere(['like', 'office.name', $this->office_name])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name])
            ->andFilterWhere(['like', 'supplier.name', $this->supplier_name])
            ->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'inventory_receipt.note', $this->note])
            ->andFilterWhere(['like', 'inventory_receipt.created_at', $this->created_at])
            ->andFilterWhere(['like', 'inventory_receipt.updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
