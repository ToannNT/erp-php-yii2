<?php

namespace api\modules\v1\frontend\pos\models\search;

use yii\data\ActiveDataProvider;
use common\models\Inventory;
use Yii;

class InventorySearch extends Inventory
{
    public function fields()
    {
        return [
            "id",
            "name",
        ];
    }

    public function rules()
    {
        return [
            ["id", "integer"],
            ["name", "string"]
        ];
    }

    public function search($params)
    {
        $query = self::find()
            ->joinWith("userOffices", true, "JOIN")
            ->andWhere(["user_office.user_id" => Yii::$app->user->identity->getId()]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
            ],
        ]);
        if (!($this->load($params, "") && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            "inventory.id" => $this->id
        ]);
        $query->andFilterWhere(["like", "inventory.name", $this->name]);
        return $dataProvider;
    }
}
