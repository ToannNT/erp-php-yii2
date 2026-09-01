<?php

namespace api\modules\v1\admin\product\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\Brand;

class BrandSearch extends Brand
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'priority', 'show_on_home', 'parent_id', 'owner_id', 'status'], 'integer'],
            [['name', 'type', 'code', 'description', 'icon', 'images', 'color', 'group_id', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
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
        $query = Brand::find()->unDelete();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
                // Quy ước sắp xếp hiển thị: priority nhỏ hiện trước (giống banner và menu danh mục),
                // hết priority thì mới tới id giảm dần cho bản ghi mới lên trên.
                'defaultOrder' => ['priority' => SORT_ASC, 'id' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'priority' => $this->priority,
            'show_on_home' => $this->show_on_home,
            'parent_id' => $this->parent_id,
            'owner_id' => $this->owner_id,
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
            ->andFilterWhere(['like', 'group_id', $this->group_id])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
