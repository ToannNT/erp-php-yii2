<?php

namespace api\modules\v1\admin\inventory\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\inventory\models\Stocktaking;

class StocktakingSearch extends Stocktaking
{
    public $office_name;
    public $inventory_name;


    public function rules()
    {
        return [
            [["id", "status", "total_difference"], "integer"],
            [["created_at", "updated_at", "stocktaking_date", "created_by", "code", "note", "office_name", "inventory_name"], "safe"]
        ];
    }

    public function search($params)
    {
        $query = self::find()
            ->joinWith("office")
            ->joinWith("inventory")
            ->joinWith("createdBy");
        if (!Yii::$app->user->can('administrator')) {
            $offices = Yii::$app->user->identity->offices;
            $query->where(['in', 'stocktaking.office_id', array_column($offices, 'id')]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(), []),
            'defaultOrder' => [
                'id' => SORT_DESC
            ]
        ]);
        $this->load($params);
        $query->andFilterWhere([
            "stocktaking.id" => $this->id,
            "stocktaking.status" => $this->status,
            "stocktaking.total_difference" => $this->total_difference,
            "stocktaking.total_adjustment" => $this->total_adjustment
        ]);
        $query->andFilterWhere(['like', 'user.username', $this->created_by])
            ->andFilterWhere(['like', 'office.name', $this->office_name])
            ->andFilterWhere(['like', 'inventory.name', $this->inventory_name])
            ->andFilterWhere(['like', 'stocktaking.note', $this->note])
            ->andFilterWhere(['like', 'stocktaking.created_at', $this->created_at])
            ->andFilterWhere(['like', 'stocktaking.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'stocktaking.stocktaking_date', $this->stocktaking_date])
            ->andFilterWhere(['like', 'stocktaking.code', $this->code]);

        return $dataProvider;
    }
}
