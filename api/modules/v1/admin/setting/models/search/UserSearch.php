<?php

namespace api\modules\v1\admin\setting\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\setting\models\User;

class UserSearch extends User
{
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['created_at', 'updated_at', 'logged_at'], 'safe'],
            [['username', 'auth_key', 'password_hash', 'email'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()
            ->joinWith("roleFirst")
            ->joinWith("offices")
            ->joinWith("suppliers")
            ->groupBy("user.id")
            ->notDelete();
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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.status' => $this->status,
        ]);

        if ($this->created_at) {
            $query->andFilterWhere(['>=', 'user.created_at', $this->created_at])
                ->andFilterWhere(["<=", 'user.created_at', $this->created_at . " 23:59:59"]);
        }
        if ($this->logged_at) {
            $query->andFilterWhere(['>=', 'user.logged_at', $this->logged_at])
                ->andFilterWhere(["<=", 'user.logged_at', $this->logged_at . " 23:59:59"]);
        }

        $query->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'user.auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'user.password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'user.email', $this->email]);

        return $dataProvider;
    }
}
