<?php

namespace api\modules\v1\admin\person\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\person\models\ContactCustomer;

/**
 * ContactCustomerSearch represents the model behind the search form about `api\modules\v1\admin\person\models\ContactCustomer`.
 */
class ContactCustomerSearch extends ContactCustomer
{

    public $contact_name;
    public $customer_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'contact_id', 'customer_id', 'status'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at', 'contact_name', 'customer_name'], 'safe'],
        ];
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
        $query = ContactCustomer::find()
            ->notDelete()
            ->joinWith("contact")
            ->joinWith("customer");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(), [
                "contact_name" => [
                    'asc' => ['contact.name' => SORT_ASC],
                    'desc' => ['contact.name' => SORT_DESC],
                    'label' => 'contact_name'
                ],
                "customer_name" => [
                    'asc' => ['customer.name' => SORT_ASC],
                    'desc' => ['customer.name' => SORT_DESC],
                    'label' => 'customer_name'
                ]
            ]),
            "defaultOrder" => [
                "id" => SORT_DESC
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'contact_customer.id' => $this->id,
            'contact_customer.contact_id' => $this->contact_id,
            'contact_customer.customer_id' => $this->customer_id,
            'contact_customer.created_at' => $this->created_at,
            'contact_customer.updated_at' => $this->updated_at,
            'contact_customer.deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(["like", "customer.name", $this->customer_name])
            ->andFilterWhere(["like", "contact.name", $this->contact_name]);

        return $dataProvider;
    }
}
