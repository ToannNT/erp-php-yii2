<?php

use yii\db\Migration;

/**
 * Thêm cột `delivery_method_id` vào bảng `order`.
 *
 * Mỗi đơn chỉ có ĐÚNG 1 phương thức giao hàng nên dùng khoá ngoại thẳng, KHÔNG làm bảng trung gian
 * kiểu `order_payment_method` (thanh toán mới cần nhiều dòng vì khách có thể trả nhiều phương thức).
 *
 * Phí giao lấy từ `delivery_method.fee` tại thời điểm đặt hàng và snapshot vào `order.delivery_fee`
 * (cột có sẵn) — sau này admin đổi giá phương thức thì đơn cũ vẫn giữ đúng số tiền đã tính.
 *
 * Class m260815_120100_add_delivery_method_to_order
 */
class m260815_120100_add_delivery_method_to_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "delivery_method_id", $this->integer()->after("delivery_fee"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "delivery_method_id");
    }
}
