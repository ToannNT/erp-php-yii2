<?php

namespace api\modules\v1\admin\general\models\search;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\general\models\Supplier;

/**
 * SupplierSearch represents the model behind the search form about `api\modules\v1\admin\general\models\Supplier`.
 */
class SupplierSearch extends Supplier
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'contact_id', 'priority', 'parent_id', 'owner_id', 'status'], 'integer'],
            [['name', 'type', 'code', 'description', 'icon', 'images', 'color', 'email', 'phone', 'website', 'fax', 'tax_code', 'address_1', 'address_2', 'note', 'supplier_status', 'group_id', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Supplier::find()->active();
        if (Yii::$app->user->can(User::ROLE_SUPPLIER)) {
            $suppliers = Yii::$app->user->identity->suppliers;
            $query->andWhere(["in", "supplier.id", array_map(function ($suppliers) {
                return $suppliers->id;
            }, $suppliers)]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                "params" => $params,
                "defaultOrder" => [
                    "id" => SORT_DESC
                ]
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'contact_id' => $this->contact_id,
            'priority' => $this->priority,
            'parent_id' => $this->parent_id,
            'owner_id' => $this->owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'color', $this->color])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'tax_code', $this->tax_code])
            ->andFilterWhere(['like', 'address_1', $this->address_1])
            ->andFilterWhere(['like', 'address_2', $this->address_2])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'supplier_status', $this->supplier_status])
            ->andFilterWhere(['like', 'group_id', $this->group_id]);

        return $dataProvider;
    }
}
