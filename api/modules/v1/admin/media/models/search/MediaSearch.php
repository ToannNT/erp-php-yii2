<?php

namespace api\modules\v1\admin\media\models\search;

use common\models\FileStorageItem;
use yii\data\ActiveDataProvider;

/**
 * Search model cho thư viện hình ảnh.
 *
 * Filter theo: `name` (LIKE), `type` (LIKE — vd "image" khớp "image/jpeg"),
 * `created_from` / `created_to` (timestamp range).
 */
class MediaSearch extends FileStorageItem
{
    public $created_from;
    public $created_to;

    public function rules()
    {
        return [
            [['name', 'type'], 'string'],
            [['created_from', 'created_to'], 'integer'],
        ];
    }

    /**
     * @param array $params Query params từ request
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = self::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type]);

        if ($this->created_from) {
            $query->andWhere(['>=', 'created_at', $this->created_from]);
        }
        if ($this->created_to) {
            $query->andWhere(['<=', 'created_at', $this->created_to]);
        }

        return $dataProvider;
    }
}
