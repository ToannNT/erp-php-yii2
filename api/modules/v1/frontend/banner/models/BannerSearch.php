<?php
namespace api\modules\v1\frontend\banner\models;
use yii\data\ActiveDataProvider;
use common\models\Banner;


class BannerSearch extends Banner{
    public function seacrh($params): ActiveDataProvider
    {
        $query = Banner::find()->active()->orderBy(['priority'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>[
                'params' =>$params,
            ],
            'sort' => [
                'params' => $params
            ]
        ]);
        $this->load($params,'');
        if (!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like','type', $this->type]);
        return $dataProvider;
    }
}