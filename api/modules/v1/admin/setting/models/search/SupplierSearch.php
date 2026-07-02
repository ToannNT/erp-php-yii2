<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\Supplier;

class SupplierSearch extends Supplier
{
    public $contact_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'contact_id', 'priority', 'parent_id', 'owner_id', 'status'], 'integer'],
            [['contact_name'], 'string'],
            [['name', 'type', 'code', 'description', 'icon', 'images', 'color', 'email', 'phone', 'website', 'fax', 'tax_code', 'address_1', 'address_2', 'note', 'supplier_status', 'group_id', 'created_at', 'updated_at', 'deleted_at', 'contact_name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Supplier::find()
            ->unDelete()
            ->joinWith("contact");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
                'pageSizeLimit' => [1, 5000]
            ]
        ]);

        $dataProvider->setSort([
            "attributes" => array_merge($this->attributes(), [
                "contact_name" => [
                    'asc' => ['contact.name' => SORT_ASC],
                    'desc' => ['contact.name' => SORT_DESC],
                    'label' => 'contact_name'
                ],
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
            'supplier.id' => $this->id,
            'supplier.contact_id' => $this->contact_id,
            'supplier.priority' => $this->priority,
            'supplier.parent_id' => $this->parent_id,
            'supplier.owner_id' => $this->owner_id,
            'supplier.deleted_at' => $this->deleted_at,
            'supplier.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'supplier.name', $this->name])
            ->andFilterWhere(['like', 'supplier.type', $this->type])
            ->andFilterWhere(['like', 'supplier.code', $this->code])
            ->andFilterWhere(['like', 'supplier.description', $this->description])
            ->andFilterWhere(['like', 'supplier.icon', $this->icon])
            ->andFilterWhere(['like', 'supplier.images', $this->images])
            ->andFilterWhere(['like', 'supplier.color', $this->color])
            ->andFilterWhere(['like', 'supplier.email', $this->email])
            ->andFilterWhere(['like', 'supplier.phone', $this->phone])
            ->andFilterWhere(['like', 'supplier.website', $this->website])
            ->andFilterWhere(['like', 'supplier.fax', $this->fax])
            ->andFilterWhere(['like', 'supplier.tax_code', $this->tax_code])
            ->andFilterWhere(['like', 'supplier.address_1', $this->address_1])
            ->andFilterWhere(['like', 'supplier.address_2', $this->address_2])
            ->andFilterWhere(['like', 'supplier.note', $this->note])
            ->andFilterWhere(['like', 'supplier.supplier_status', $this->supplier_status])
            ->andFilterWhere(['like', 'supplier.group_id', $this->group_id])
            ->andFilterWhere(['like', 'supplier.created_at', $this->created_at])
            ->andFilterWhere(['like', 'supplier.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'contact.name', $this->contact_name]);
        return $dataProvider;
    }
}
