<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\Brand;
use api\modules\v1\admin\product\models\Category;
use api\modules\v1\admin\product\models\CategoryBrand;
use common\validators\IsArrayValidator;
use Yii;

/**
 * Form dùng cho create/update Category kèm gán nhiều nhãn hiệu (multiple select).
 *
 * Mirror của {@see BrandForm} nhưng theo chiều ngược lại: BrandForm nhận `categories`,
 * form này nhận `brands` và đồng bộ cùng bảng nối `category_brand`.
 *
 * Lưu ý: relation trả ra tên là `batchBrands` (không phải `brands`) vì `$brands` đã là
 * property nhận input — PHP ưu tiên property hơn magic getter nên đặt trùng tên sẽ làm chết relation.
 */
class CategoryForm extends Category
{
    /**
     * Danh sách id nhãn hiệu, ví dụ [1, 2, 3].
     *
     * - Không gửi field (null) => giữ nguyên các nhãn hiệu đang có.
     * - Gửi [] => bỏ hết nhãn hiệu của category.
     *
     * @var int[]|null
     */
    public $brands;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ["brands", IsArrayValidator::class],
            ["brands", "validateBrands"],
        ]);
    }

    /**
     * Kiểm tra mọi id nhãn hiệu đều tồn tại & chưa bị xoá, đồng thời chuẩn hoá về mảng int không trùng.
     */
    public function validateBrands($attribute)
    {
        if (!is_array($this->$attribute)) {
            return;
        }
        $ids = array_values(array_unique(array_map("intval", $this->$attribute)));
        if (!$ids) {
            $this->$attribute = [];
            return;
        }
        $exists = array_map("intval", Brand::find()
            ->select(["id"])
            ->where(["id" => $ids])
            ->andWhere(["!=", "status", Brand::STATUS_DELETE])
            ->column());
        $missing = array_diff($ids, $exists);
        if ($missing) {
            $this->addError($attribute, Yii::t("api", "Brand not found: {ids}", [
                "ids" => implode(", ", $missing),
            ]));
            return;
        }
        $this->$attribute = $ids;
    }

    /**
     * Đồng bộ bảng nối `category_brand` theo diff: thêm cái mới, xoá cái bị bỏ, giữ cái không đổi.
     * Gọi SAU khi save() thành công (cần có $this->id).
     */
    public function createOrDeleteBrand()
    {
        if (!is_array($this->brands)) {
            return;
        }
        $new = array_values(array_unique(array_map("intval", $this->brands)));
        $old = array_map("intval", CategoryBrand::find()
            ->select(["brand_id"])
            ->where(["category_id" => $this->id])
            ->column());

        foreach (array_diff($new, $old) as $brandId) {
            (new CategoryBrand([
                "category_id" => $this->id,
                "brand_id" => $brandId,
                "status" => CategoryBrand::STATUS_ACTIVE,
            ]))->save(false);
        }

        $removed = array_values(array_diff($old, $new));
        if ($removed) {
            CategoryBrand::deleteAll(["category_id" => $this->id, "brand_id" => $removed]);
        }
    }
}
