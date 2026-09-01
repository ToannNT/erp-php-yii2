<?php

use yii\db\Migration;

/**
 * Thêm cờ `show_on_home` cho `brand` và `category`: chọn thủ công cái nào được lên trang chủ.
 *
 * Khác `status` — `status` là "còn dùng hay không" (tắt là mất khỏi mọi nơi), còn `show_on_home`
 * chỉ quyết định có được ưu tiên lên trang chủ. Nhãn hiệu bỏ cờ này vẫn xem/mua bình thường ở
 * trang danh sách và trang sản phẩm.
 *
 * Cột `priority` KHÔNG thêm mới — đã có sẵn từ migration tạo bảng (int, default 0), chỉ thiếu
 * index. Quy ước sắp xếp giữ đúng như banner và menu danh mục đang chạy: số nhỏ hiện trước.
 */
class m260901_100000_add_show_on_home extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%brand}}',
            'show_on_home',
            $this->tinyInteger(1)->notNull()->defaultValue(0)->after('priority')
        );
        $this->addColumn(
            '{{%category}}',
            'show_on_home',
            $this->tinyInteger(1)->notNull()->defaultValue(0)->after('priority')
        );

        // Đúng hình dạng truy vấn của trang chủ: WHERE show_on_home = 1 ORDER BY priority.
        $this->createIndex('idx_brand_show_on_home', '{{%brand}}', ['show_on_home', 'priority']);
        $this->createIndex('idx_category_show_on_home', '{{%category}}', ['show_on_home', 'priority']);

        // Index composite ở trên không phục vụ được ORDER BY priority khi không lọc show_on_home,
        // mà danh sách admin lẫn danh sách enduser đều sort mặc định theo priority.
        $this->createIndex('idx_brand_priority', '{{%brand}}', ['priority']);
        $this->createIndex('idx_category_priority', '{{%category}}', ['priority']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_category_priority', '{{%category}}');
        $this->dropIndex('idx_brand_priority', '{{%brand}}');
        $this->dropIndex('idx_category_show_on_home', '{{%category}}');
        $this->dropIndex('idx_brand_show_on_home', '{{%brand}}');

        $this->dropColumn('{{%category}}', 'show_on_home');
        $this->dropColumn('{{%brand}}', 'show_on_home');
    }
}
