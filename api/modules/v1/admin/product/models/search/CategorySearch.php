<?php

namespace api\modules\v1\admin\product\models\search;

use yii\data\ActiveDataProvider;
use api\modules\v1\admin\product\models\CategoryBrand;
use api\modules\v1\admin\product\models\Category;

class CategorySearch extends Category
{
    /** @var int|int[]|string|null id nhãn hiệu, nhận 1 giá trị hoặc danh sách "1,2,3" */
    public $brand_id;

    /**
     * `1` => trả PHẲNG cả cha lẫn con (không lồng).
     *
     * Mặc định list chỉ trả danh mục gốc kèm `children`, nhưng chính ô "chọn danh mục cha"
     * và ô chọn danh mục của sản phẩm lại cần danh sách phẳng để đổ options.
     *
     * @var int|string|null
     */
    public $flat;

    public function rules()
    {
        return [
            [['id', 'priority', 'show_on_home', 'parent_id', 'owner_id', 'status'], 'integer'],
            [['name', 'type', 'code', 'icon', 'images', 'color', 'description', 'slug', 'group_id', 'created_at', 'updated_at', 'deleted_at', 'brand_id', 'flat'], 'safe'],
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

        $query = Category::find()->unDelete()->with(["batchBrands", "children"]);

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
        // Mặc định chỉ trả danh mục GỐC, mỗi cái kèm `children` (yêu cầu của leader:
        // "get list chỉ get category bình thường, cái nào có con thì show ra kiểu list").
        // `?flat=1` để lấy phẳng, `?parent_id=44` để lấy con của 1 cha.
        if (!$this->flat && ($this->parent_id === null || $this->parent_id === "")) {
            $query->andWhere(["parent_id" => null]);
        }

        if (!$this->validate()) {
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
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'color', $this->color])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'group_id', $this->group_id])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        if ($this->brand_id) {
            // Dùng subquery thay vì joinWith để 1 category không bị nhân dòng khi khớp nhiều nhãn hiệu.
            $brandIds = is_array($this->brand_id) ? $this->brand_id : explode(",", (string) $this->brand_id);
            $query->andWhere(["id" => CategoryBrand::find()
                ->select(["category_id"])
                ->where(["brand_id" => $brandIds])]);
        }

        return $dataProvider;
    }
}
