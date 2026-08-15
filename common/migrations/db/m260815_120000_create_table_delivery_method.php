<?php

use yii\db\Migration;

/**
 * Tạo bảng `delivery_method` — danh mục PHƯƠNG THỨC GIAO HÀNG cho website.
 *
 * Cấu trúc cố tình làm đối xứng với `payment_method` để dùng chung một kiểu API/logic:
 * name/code/status/is_default. Khác duy nhất là có thêm cột `fee` — phí giao cố định
 * theo phương thức, `0` = miễn phí.
 *
 * Lưu ý phân biệt với các thứ đã có:
 * - `delivery_fee` (bảng): bảng phí ship rời, dùng cho POS/admin qua `data_delivery_fee`. Giữ nguyên.
 * - `order.delivery` (text): cột chết từ trước, không code nào đọc/ghi. KHÔNG dùng.
 * - `order_ship`: đơn vận chuyển qua đối tác GHN/GHTK, khác tầng nghiệp vụ.
 *
 * Class m260815_120000_create_table_delivery_method
 */
class m260815_120000_create_table_delivery_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("delivery_method", [
            "id" => $this->primaryKey(),
            "name" => $this->string(200),
            "code" => $this->string(100),
            "fee" => $this->double()->defaultValue(0),
            "status" => $this->integer(),
            "is_default" => $this->boolean(),
            "created_by" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime(),
            "deleted_at" => $this->dateTime()
        ]);

        $now = date("Y-m-d H:i:s");
        $this->batchInsert("delivery_method", [
            "name", "code", "fee", "status", "is_default", "created_by", "created_at", "updated_at"
        ], [
            ["Giao hàng tiêu chuẩn", "STANDARD", 30000, 1, 1, 1, $now, $now],
            ["Giao hàng nhanh", "EXPRESS", 50000, 1, 0, 1, $now, $now],
            ["Nhận tại cửa hàng", "PICKUP", 0, 1, 0, 1, $now, $now],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("delivery_method");
    }
}
