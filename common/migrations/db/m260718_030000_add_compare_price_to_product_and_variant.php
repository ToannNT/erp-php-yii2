<?php

use yii\db\Migration;

/**
 * Thêm cột `compare_price` (giá gốc để gạch ngang / so sánh) vào bảng `product` và `product_variant`.
 *
 * - `unit_price` vẫn là GIÁ THẬT khách trả (đã là giá sau giảm nếu có sale) — mọi logic đơn hàng KHÔNG đổi.
 * - `compare_price` chỉ để HIỂN THỊ: khi `compare_price > unit_price` thì frontend gạch ngang `compare_price`
 *   và hiện `unit_price` như giá sale. `0` = không có giá gốc, chỉ hiện `unit_price` bình thường.
 *
 * Class m260718_030000_add_compare_price_to_product_and_variant
 */
class m260718_030000_add_compare_price_to_product_and_variant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product', 'compare_price', $this->double()->defaultValue(0)->after('sll_price'));
        $this->addColumn('product_variant', 'compare_price', $this->double()->defaultValue(0)->after('sll_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'compare_price');
        $this->dropColumn('product_variant', 'compare_price');
    }
}
