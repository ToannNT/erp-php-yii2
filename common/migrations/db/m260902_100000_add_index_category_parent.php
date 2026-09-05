<?php

use yii\db\Migration;

/**
 * Index cho danh mục 2 cấp.
 *
 * Cột `parent_id` KHÔNG thêm mới — đã có từ migration tạo bảng (int, nullable), chỉ là chưa ai
 * dùng (12/12 danh mục đang NULL) và `rules()` phía admin chưa khai nên không lưu được.
 *
 * Index `(parent_id, priority)` phục vụ đúng 2 truy vấn của tính năng:
 *   - lấy danh mục con của 1 cha, sắp theo priority
 *   - lọc danh mục gốc: WHERE parent_id IS NULL
 */
class m260902_100000_add_index_category_parent extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_category_parent', '{{%category}}', ['parent_id', 'priority']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_category_parent', '{{%category}}');
    }
}
