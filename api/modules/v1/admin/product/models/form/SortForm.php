<?php

namespace api\modules\v1\admin\product\models\form;

use common\validators\IsArrayValidator;
use Throwable;
use Yii;
use yii\base\Model;

/**
 * Sắp xếp thứ tự hiển thị hàng loạt cho Category / Brand.
 *
 * Cả 2 bảng đều có cột `priority` (int, default 0) và dùng chung quy ước "số nhỏ hiện trước"
 * như banner và menu danh mục đang chạy. Gộp về một form để 2 controller không copy logic.
 *
 * Body:
 *   {"items": [{"id": 49, "priority": 1}, {"id": 52, "priority": 2}]}
 *   {"items": [{"id": 49}, {"id": 52}]}   // thiếu `priority` => lấy vị trí trong mảng: 1, 2
 */
class SortForm extends Model
{
    /**
     * Danh sách {id, priority}. `priority` không bắt buộc.
     *
     * @var array[]|null
     */
    public $items;

    /**
     * Class model sẽ cập nhật, set từ controller.
     *
     * CHỦ Ý không khai rule nào cho thuộc tính này để nó KHÔNG nằm trong safe attributes —
     * nếu không, client gửi kèm `modelClass` trong body là ghi được lên bảng bất kỳ.
     *
     * @var string|\yii\db\ActiveRecord
     */
    public $modelClass;

    /** @var array<int,int> map id => priority, chuẩn hoá sau khi validate */
    private array $normalized = [];

    public function formName()
    {
        return "";
    }

    public function rules()
    {
        return [
            [["items"], "required"],
            [["items"], IsArrayValidator::class],
            [["items"], "validateItems"],
        ];
    }

    /**
     * Kiểm tra cấu trúc từng phần tử, chống trùng id, và mọi id đều tồn tại & chưa bị xoá mềm.
     */
    public function validateItems($attribute)
    {
        if (!is_array($this->$attribute)) {
            return;
        }
        if (!$this->$attribute) {
            $this->addError($attribute, Yii::t("api", "Items can not be empty"));
            return;
        }

        $map = [];
        foreach (array_values($this->$attribute) as $index => $item) {
            if (!is_array($item) || !isset($item["id"]) || !is_numeric($item["id"])) {
                $this->addError($attribute, Yii::t("api", "Item {index} must have a numeric id", [
                    "index" => $index,
                ]));
                return;
            }
            $id = (int) $item["id"];
            if (isset($map[$id])) {
                $this->addError($attribute, Yii::t("api", "Duplicated id: {id}", ["id" => $id]));
                return;
            }
            if (isset($item["priority"]) && $item["priority"] !== "") {
                if (!is_numeric($item["priority"])) {
                    $this->addError($attribute, Yii::t("api", "Item {index} has invalid priority", [
                        "index" => $index,
                    ]));
                    return;
                }
                $map[$id] = (int) $item["priority"];
                continue;
            }
            // Không gửi `priority` thì lấy đúng vị trí trong mảng — FE kéo thả xong gửi nguyên
            // thứ tự đang hiển thị là đủ, không phải tự đánh số.
            $map[$id] = $index + 1;
        }

        $class = $this->modelClass;
        $exists = array_map("intval", $class::find()
            ->select(["id"])
            ->where(["id" => array_keys($map)])
            ->andWhere(["!=", "status", $class::STATUS_DELETE])
            ->column());
        $missing = array_diff(array_keys($map), $exists);
        if ($missing) {
            $this->addError($attribute, Yii::t("api", "Record not found: {ids}", [
                "ids" => implode(", ", $missing),
            ]));
            return;
        }

        $this->normalized = $map;
    }

    /**
     * Ghi `priority` cho từng bản ghi. Gọi SAU khi validate() thành công.
     *
     * Dùng updateAll() chứ không save(): tránh chạy beforeSave (SluggableBehavior sinh lại slug)
     * và không bump `updated_at` — kéo thả thứ tự không phải là sửa nội dung bản ghi.
     */
    public function apply(): bool
    {
        if (!$this->normalized) {
            return true;
        }
        $class = $this->modelClass;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->normalized as $id => $priority) {
                $class::updateAll(["priority" => $priority], ["id" => $id]);
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $this->addError("items", $e->getMessage());
            return false;
        }
    }

    /**
     * Map id => priority đã áp dụng, trả về cho FE đối chiếu lại thứ tự vừa lưu.
     *
     * @return array<int,int>
     */
    public function getApplied(): array
    {
        return $this->normalized;
    }
}
