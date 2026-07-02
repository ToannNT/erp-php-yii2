<?php

namespace api\modules\v1\admin\cms\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\admin\cms\models\SystemCmsCollection;

/**
 * SystemCmsCollectionSearch represents the model behind the search form of `api\modules\v1\admin\cms\models\SystemCmsCollection`.
 */
class SystemCmsCollectionSearch extends SystemCmsCollection
{
    public function fields()
    {
        return [
            "id",
            "name",
            "user_id",
            "schemas",
            "created_at",
            "updated_at"
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'status'], 'integer'],
            [['name', 'schemas', 'indexs', 'list_rule', 'view_rule', 'create_rule', 'update_rule', 'delete_rule', 'options', 'external_data', 'created_at', 'updated_at'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'schemas', $this->schemas])
            ->andFilterWhere(['like', 'indexs', $this->indexs])
            ->andFilterWhere(['like', 'list_rule', $this->list_rule])
            ->andFilterWhere(['like', 'view_rule', $this->view_rule])
            ->andFilterWhere(['like', 'create_rule', $this->create_rule])
            ->andFilterWhere(['like', 'update_rule', $this->update_rule])
            ->andFilterWhere(['like', 'delete_rule', $this->delete_rule])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'external_data', $this->external_data]);

        return $dataProvider;
    }
}
